<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\MembershipTier;
use App\Models\Service;
use App\Models\Specialist;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Regex pattern for valid Saudi mobile numbers (e.g., 05XXXXXXXX, +9665XXXXXXXX, 009665XXXXXXXX, 5XXXXXXXX).
     */
    public const SAUDI_PHONE_REGEX = '/^(?:\+966|00966|966|0)?5[0-9]{8}$/';

    /**
     * Step 1 of verification: Validate Saudi phone number, check if customer exists in DB,
     * and if found, immediately create the booking in the database.
     */
    public function verifyPhone(Request $request): JsonResponse
    {
        $rawPhone = $this->sanitizeDigits((string) $request->input('phone', ''));
        $request->merge(['phone' => $rawPhone]);

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:'.self::SAUDI_PHONE_REGEX],
            'service_id' => ['required', 'string'],
            'specialist_id' => ['required', 'string'],
            'booking_date' => ['nullable', 'string'],
            'booking_time' => ['nullable', 'string'],
        ], [
            'phone.required' => 'يرجى إدخال رقم الجوال للمتابعة.',
            'phone.regex' => 'يرجى إدخال رقم جوال سعودي صحيح يبدأ بـ 05 ويتكون من 10 أرقام (مثال: 0551234567).',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة أولاً.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية أولاً.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $normalizedPhone = $this->normalizeSaudiPhone($rawPhone);

        $customer = Customer::where('phone', $normalizedPhone)
            ->orWhere('phone', $rawPhone)
            ->first();

        if (! $customer) {
            return response()->json([
                'success' => true,
                'exists' => false,
                'status' => 'needs_registration',
                'phone' => $normalizedPhone,
                'message' => 'رقم الجوال غير مسجل مسبقاً. يرجى إكمال التسجيل عبر حساب Google لتأكيد حجزكِ.',
            ]);
        }

        $bookingPayload = $this->createBookingForCustomer(
            customer: $customer,
            serviceCode: (string) $request->input('service_id'),
            specialistSlug: (string) $request->input('specialist_id'),
            dateLabel: (string) $request->input('booking_date', 'غداً - السبت'),
            timeSlot: (string) $request->input('booking_time', '05:30 م')
        );

        return response()->json([
            'success' => true,
            'exists' => true,
            'status' => 'confirmed',
            'message' => "تم تأكيد الحجز بنجاح باسم {$customer->name}",
            'customer' => [
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
            ],
            'booking' => $bookingPayload,
        ]);
    }

    /**
     * Redirect to Google OAuth 2.0 consent screen with booking state.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $rawPhone = $this->sanitizeDigits((string) $request->query('phone', ''));
        $normalizedPhone = $this->normalizeSaudiPhone($rawPhone);

        $bookingContext = [
            'phone' => $normalizedPhone,
            'service_id' => (string) $request->query('service_id', ''),
            'specialist_id' => (string) $request->query('specialist_id', ''),
            'booking_date' => (string) $request->query('booking_date', 'غداً - السبت'),
            'booking_time' => (string) $request->query('booking_time', '05:30 م'),
        ];

        session(['pending_google_booking' => $bookingContext]);

        $clientId = (string) config('services.google.client_id');
        $redirectUri = $this->resolveGoogleRedirectUri($request);
        $state = base64_encode((string) json_encode($bookingContext, JSON_UNESCAPED_UNICODE));

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $state,
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    /**
     * Handle Google OAuth 2.0 callback, create customer & booking in DB, and notify the opener window.
     */
    public function handleGoogleCallback(Request $request): Response
    {
        $code = (string) $request->query('code', '');
        $stateRaw = (string) $request->query('state', '');
        $decodedState = $stateRaw !== '' ? json_decode((string) base64_decode($stateRaw), true) : null;
        $bookingContext = is_array($decodedState) ? $decodedState : (array) session('pending_google_booking', []);

        if ($code === '') {
            return $this->renderPopupCallbackScript([
                'success' => false,
                'status' => 'oauth_cancelled',
                'message' => 'تم إلغاء عملية تسجيل الدخول عبر Google.',
            ]);
        }

        $redirectUri = $this->resolveGoogleRedirectUri($request);
        $googleUser = $this->exchangeGoogleCodeForUser($code, $redirectUri);

        if (! $googleUser) {
            return $this->renderPopupCallbackScript([
                'success' => false,
                'status' => 'oauth_error',
                'message' => 'تعذر التحقق من حساب Google، يرجى المحاولة مرة أخرى.',
            ]);
        }

        $normalizedPhone = $this->normalizeSaudiPhone((string) ($bookingContext['phone'] ?? '0550000000'));
        $email = $googleUser['email'];
        $ownerName = $googleUser['name'] !== '' ? $googleUser['name'] : $this->extractNameFromEmail($email);
        $avatarUrl = $googleUser['picture'] ?? null;

        $customer = $this->findOrCreateGoogleCustomer($normalizedPhone, $email, $ownerName, $avatarUrl);

        $bookingPayload = $this->createBookingForCustomer(
            customer: $customer,
            serviceCode: (string) ($bookingContext['service_id'] ?? ''),
            specialistSlug: (string) ($bookingContext['specialist_id'] ?? ''),
            dateLabel: (string) ($bookingContext['booking_date'] ?? 'غداً - السبت'),
            timeSlot: (string) ($bookingContext['booking_time'] ?? '05:30 م')
        );

        session()->forget('pending_google_booking');

        return $this->renderPopupCallbackScript([
            'success' => true,
            'exists' => true,
            'registered_via_google' => true,
            'status' => 'confirmed',
            'message' => "تم التسجيل عبر Google وتأكيد الحجز باسم {$customer->name}",
            'customer' => [
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
            ],
            'booking' => $bookingPayload,
        ]);
    }

    /**
     * Step 2 (AJAX): Register new customer via Google (using OAuth code, access_token, or Google profile)
     * and immediately create the booking in the database.
     */
    public function registerWithGoogle(Request $request): JsonResponse
    {
        $rawPhone = $this->sanitizeDigits((string) $request->input('phone', ''));
        $request->merge(['phone' => $rawPhone]);

        // If a Google OAuth authorization code or access_token is sent from Google Identity Services popup
        $oauthCode = trim((string) $request->input('code', ''));
        $accessToken = trim((string) $request->input('access_token', ''));

        if ($oauthCode !== '') {
            $googleUser = $this->exchangeGoogleCodeForUser($oauthCode, 'postmessage');
            if ($googleUser) {
                $request->merge([
                    'email' => $googleUser['email'],
                    'name' => $googleUser['name'],
                    'avatar_url' => $googleUser['picture'] ?? null,
                ]);
            }
        } elseif ($accessToken !== '') {
            $googleUser = $this->fetchGoogleUserFromAccessToken($accessToken);
            if ($googleUser) {
                $request->merge([
                    'email' => $googleUser['email'],
                    'name' => $googleUser['name'],
                    'avatar_url' => $googleUser['picture'] ?? null,
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:'.self::SAUDI_PHONE_REGEX],
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'service_id' => ['required', 'string'],
            'specialist_id' => ['required', 'string'],
            'booking_date' => ['nullable', 'string'],
            'booking_time' => ['nullable', 'string'],
        ], [
            'phone.required' => 'يرجى إدخال رقم الجوال السعودي.',
            'phone.regex' => 'يرجى إدخال رقم جوال سعودي صحيح (مثال: 0551234567).',
            'email.required' => 'تعذر الحصول على البريد الإلكتروني من حساب Google.',
            'email.email' => 'يرجى التأكد من صحة البريد الإلكتروني لحساب Google.',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $normalizedPhone = $this->normalizeSaudiPhone($rawPhone);
        $email = trim((string) $request->input('email'));
        $providedName = trim((string) $request->input('name', ''));
        $avatarUrl = $request->input('avatar_url');

        // Derive owner name from email if not explicitly provided
        $ownerName = $providedName !== ''
            ? $providedName
            : $this->extractNameFromEmail($email);

        $customer = $this->findOrCreateGoogleCustomer(
            normalizedPhone: $normalizedPhone,
            email: $email,
            ownerName: $ownerName,
            avatarUrl: is_string($avatarUrl) ? $avatarUrl : null
        );

        $bookingPayload = $this->createBookingForCustomer(
            customer: $customer,
            serviceCode: (string) $request->input('service_id'),
            specialistSlug: (string) $request->input('specialist_id'),
            dateLabel: (string) $request->input('booking_date', 'غداً - السبت'),
            timeSlot: (string) $request->input('booking_time', '05:30 م')
        );

        return response()->json([
            'success' => true,
            'exists' => true,
            'registered_via_google' => true,
            'status' => 'confirmed',
            'message' => "تم التسجيل عبر Google وتأكيد الحجز باسم {$customer->name}",
            'customer' => [
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
            ],
            'booking' => $bookingPayload,
        ]);
    }

    /**
     * Find or create the customer in the database after Google sign-in.
     */
    private function findOrCreateGoogleCustomer(
        string $normalizedPhone,
        string $email,
        string $ownerName,
        ?string $avatarUrl = null
    ): Customer {
        return DB::transaction(function () use ($normalizedPhone, $email, $ownerName, $avatarUrl): Customer {
            $existing = Customer::where('phone', $normalizedPhone)->first();
            if ($existing) {
                $existing->update([
                    'name' => $ownerName,
                    'email' => $email,
                    'avatar_url' => $avatarUrl ?: $existing->avatar_url,
                ]);

                return $existing;
            }

            $defaultTierId = MembershipTier::where('slug', 'silver')->value('id')
                ?? MembershipTier::orderBy('min_points_required')->value('id');

            do {
                $customerCode = 'HL-'.random_int(1000, 9999);
            } while (Customer::where('customer_code', $customerCode)->exists());

            return Customer::create([
                'membership_tier_id' => $defaultTierId,
                'customer_code' => $customerCode,
                'name' => $ownerName,
                'phone' => $normalizedPhone,
                'email' => $email,
                'avatar_url' => $avatarUrl ?: 'https://ui-avatars.com/api/?name='.urlencode($ownerName).'&background=211C18&color=C5A373&bold=true',
                'total_points' => 0,
                'is_active' => true,
            ]);
        });
    }

    /**
     * Exchange Google OAuth 2.0 authorization code for user profile using GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET.
     *
     * @return ?array{email: string, name: string, picture: ?string}
     */
    private function exchangeGoogleCodeForUser(string $code, string $redirectUri): ?array
    {
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => (string) config('services.google.client_id'),
            'client_secret' => (string) config('services.google.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ]);

        if (! $tokenResponse->successful()) {
            return null;
        }

        $accessToken = (string) $tokenResponse->json('access_token', '');
        if ($accessToken === '') {
            return null;
        }

        return $this->fetchGoogleUserFromAccessToken($accessToken);
    }

    /**
     * Fetch Google user profile from access token.
     *
     * @return ?array{email: string, name: string, picture: ?string}
     */
    private function fetchGoogleUserFromAccessToken(string $accessToken): ?array
    {
        $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
        if (! $userResponse->successful()) {
            return null;
        }

        $email = trim((string) $userResponse->json('email', ''));
        if ($email === '') {
            return null;
        }

        $name = trim((string) $userResponse->json('name', ''));
        $picture = $userResponse->json('picture');

        return [
            'email' => $email,
            'name' => $name !== '' ? $name : $this->extractNameFromEmail($email),
            'picture' => is_string($picture) ? $picture : null,
        ];
    }

    /**
     * Resolve the Google OAuth callback URI matching the current request origin or configured redirect URI.
     */
    private function resolveGoogleRedirectUri(Request $request): string
    {
        $configured = (string) config('services.google.redirect', '');
        if (str_starts_with($configured, 'http://') || str_starts_with($configured, 'https://')) {
            return $configured;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/').'/auth/google/callback';
    }

    /**
     * Render a minimal HTML response for the OAuth popup that notifies the parent window via postMessage and closes itself.
     *
     * @param  array<string, mixed>  $payload
     */
    private function renderPopupCallbackScript(array $payload): Response
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول عبر Google - هيرلاين</title>
</head>
<body style="background:#FAF8F5;color:#211C18;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;">
    <div style="text-align:center;">
        <p style="font-weight:bold;">جاري إتمام الحجز والعودة لصفحة الصالون...</p>
    </div>
    <script>
        (function() {
            const payload = {$json};
            if (window.opener && !window.opener.closed) {
                window.opener.postMessage({
                    type: 'HAIRLINE_GOOGLE_AUTH_RESULT',
                    payload: payload
                }, '*');
                window.close();
            } else {
                window.location.href = '/booking';
            }
        })();
    </script>
</body>
</html>
HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Persist Booking and BookingItem records directly in the database and return formatted payload.
     *
     * @return array{
     *     id: string,
     *     db_id: int,
     *     customerName: string,
     *     customerPhone: string,
     *     customerEmail: ?string,
     *     categoryTitle: string,
     *     serviceName: string,
     *     specialistName: string,
     *     date: string,
     *     bookingDateIso: string,
     *     time: string,
     *     price: string
     * }
     */
    private function createBookingForCustomer(
        Customer $customer,
        string $serviceCode,
        string $specialistSlug,
        string $dateLabel,
        string $timeSlot
    ): array {
        $service = Service::with('category')->where('code', $serviceCode)->first()
            ?? Service::with('category')->where('id', $serviceCode)->first()
            ?? Service::with('category')->firstOrFail();

        $specialist = Specialist::where('slug', $specialistSlug)->first()
            ?? Specialist::where('id', $specialistSlug)->first()
            ?? Specialist::firstOrFail();

        $bookingDate = $this->resolveBookingDate($dateLabel);
        $startTime24 = $this->parseTimeSlotTo24Hour($timeSlot);
        $durationMinutes = max(15, (int) ($service->duration_minutes ?: 60));
        $endTime24 = Carbon::createFromFormat('H:i:s', $startTime24)
            ->addMinutes($durationMinutes)
            ->format('H:i:s');

        $price = (float) $service->base_price;
        $vatAmount = round($price * 15 / 115, 2);
        $depositRequired = round(max($price * 0.30, min(50.0, $price)), 2);

        $booking = DB::transaction(function () use (
            $customer,
            $service,
            $specialist,
            $bookingDate,
            $startTime24,
            $endTime24,
            $price,
            $vatAmount,
            $depositRequired,
            $durationMinutes
        ): Booking {
            do {
                $bookingNumber = (string) random_int(1000, 9999);
            } while (Booking::where('booking_number', $bookingNumber)->exists());

            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'customer_id' => $customer->id,
                'customer_address_id' => null,
                'booking_type' => 'salon',
                'booking_date' => $bookingDate->toDateString(),
                'overall_start_time' => $startTime24,
                'overall_end_time' => $endTime24,
                'status' => 'confirmed',
                'subtotal_amount' => $price,
                'discount_amount' => 0.00,
                'vat_amount' => $vatAmount,
                'total_amount' => $price,
                'deposit_required' => $depositRequired,
                'deposit_paid' => 0.00,
                'payment_status' => 'unpaid',
                'admin_notes' => [
                    'ar' => 'حجز إلكتروني مباشر من صفحة الحجز',
                    'en' => 'Direct online booking from booking page',
                ],
            ]);

            BookingItem::create([
                'booking_id' => $booking->id,
                'service_id' => $service->id,
                'specialist_id' => $specialist->id,
                'hair_length_id' => null,
                'unit_price' => $price,
                'duration_minutes' => $durationMinutes,
                'scheduled_start_time' => $startTime24,
                'scheduled_end_time' => $endTime24,
                'status' => 'confirmed',
            ]);

            return $booking;
        });

        $nameAr = $specialist->getTranslation('name', 'ar');
        $nameEn = $specialist->getTranslation('name', 'en');
        $specialistFullName = $nameEn ? "{$nameAr} | {$nameEn}" : $nameAr;

        return [
            'id' => $booking->booking_number,
            'db_id' => $booking->id,
            'customerName' => $customer->name,
            'customerPhone' => $customer->phone,
            'customerEmail' => $customer->email,
            'categoryTitle' => $service->category?->getTranslation('name', 'ar') ?? 'خدمات الصالون',
            'serviceName' => $service->getTranslation('name', 'ar'),
            'specialistName' => $specialistFullName,
            'date' => $dateLabel !== '' ? $dateLabel : $bookingDate->toDateString(),
            'bookingDateIso' => $bookingDate->toDateString(),
            'time' => $timeSlot !== '' ? $timeSlot : '05:30 م',
            'price' => ((int) $price).' ر.س',
        ];
    }

    /**
     * Convert Eastern Arabic numerals and strip whitespace/hyphens.
     */
    private function sanitizeDigits(string $input): string
    {
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $converted = str_replace($eastern, $western, trim($input));

        return (string) preg_replace('/[\s\-\(\)]+/', '', $converted);
    }

    /**
     * Normalize any valid Saudi mobile format (+9665XXXXXXXX, 009665XXXXXXXX, 9665XXXXXXXX, 5XXXXXXXX) to 05XXXXXXXX.
     */
    private function normalizeSaudiPhone(string $phone): string
    {
        $clean = $this->sanitizeDigits($phone);

        if (preg_match('/^(?:\+966|00966|966)(5[0-9]{8})$/', $clean, $matches)) {
            return '0'.$matches[1];
        }

        if (preg_match('/^(5[0-9]{8})$/', $clean, $matches)) {
            return '0'.$matches[1];
        }

        return $clean;
    }

    /**
     * Extract a readable name from an email address if name was not explicitly entered.
     */
    private function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0] ?? 'عميلة هيرلاين';
        $cleaned = str_replace(['.', '_', '-'], ' ', $localPart);
        $cleaned = trim((string) preg_replace('/\d+/', '', $cleaned));

        return $cleaned !== '' ? mb_convert_case($cleaned, MB_CASE_TITLE, 'UTF-8') : $localPart;
    }

    /**
     * Resolve Arabic date label or ISO date string into a Carbon date instance.
     */
    private function resolveBookingDate(string $dateLabel): Carbon
    {
        $today = Carbon::today('Asia/Riyadh');

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($dateLabel))) {
            return Carbon::parse($dateLabel, 'Asia/Riyadh');
        }

        if (str_contains($dateLabel, 'اليوم')) {
            return $today;
        }

        if (str_contains($dateLabel, 'غداً')) {
            return $today->copy()->addDay();
        }

        if (str_contains($dateLabel, 'الأحد')) {
            return $today->copy()->next(Carbon::SUNDAY);
        }

        if (str_contains($dateLabel, 'الإثنين') || str_contains($dateLabel, 'الاثنين')) {
            return $today->copy()->next(Carbon::MONDAY);
        }

        return $today->copy()->addDay();
    }

    /**
     * Convert Arabic time slot string (e.g. "05:30 م" or "11:30 ص") to 24-hour "H:i:s".
     */
    private function parseTimeSlotTo24Hour(string $timeSlot): string
    {
        $cleaned = trim(str_replace(['ص', 'م'], '', $timeSlot));
        if (! preg_match('/^(\d{1,2}):(\d{2})/', $cleaned, $matches)) {
            return '17:30:00';
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];

        if (str_contains($timeSlot, 'م') && $hours < 12) {
            $hours += 12;
        } elseif (str_contains($timeSlot, 'ص') && $hours === 12) {
            $hours = 0;
        }

        return sprintf('%02d:%02d:00', $hours, $minutes);
    }
}
