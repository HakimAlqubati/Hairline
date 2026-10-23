<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\MembershipTier;
use App\Models\Service;
use App\Models\Specialist;
use App\Rules\AvailableSpecialistSlot;
use App\Rules\SaudiPhoneNumber;
use App\Rules\WithinSpecialistShift;
use App\Services\Booking\SpecialistAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /**
     * Regex pattern for valid Saudi mobile numbers (kept for reference compatibility).
     */
    public const SAUDI_PHONE_REGEX = SpecialistAvailabilityService::SAUDI_PHONE_REGEX;

    public function __construct(
        private readonly SpecialistAvailabilityService $availabilityService
    ) {}

    /**
     * Return shift-based time slots and real-time database booking conflicts for a specialist and date.
     */
    public function getAvailability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'specialist_id' => ['required', 'string'],
            'service_id' => ['nullable', 'string'],
            'booking_date' => ['nullable', 'string'],
        ], [
            'specialist_id.required' => 'يرجى تحديد الأخصائية لعرض الأوقات المتاحة.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $specialist = $this->availabilityService->resolveSpecialist((string) $request->query('specialist_id'));
        if (! $specialist) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'الأخصائية المختارة غير متاحة حالياً.',
            ], 404);
        }

        $service = $this->availabilityService->resolveService((string) $request->query('service_id', ''));
        $durationMinutes = $this->availabilityService->resolveDurationMinutes($specialist, $service);
        $date = $this->availabilityService->resolveBookingDate((string) $request->query('booking_date', ''));

        $availability = $this->availabilityService->getAvailabilityForSpecialistDate(
            specialist: $specialist,
            date: $date,
            durationMinutes: $durationMinutes
        );

        return response()->json([
            'success' => true,
            ...$availability,
        ]);
    }

    /**
     * Step 1 of verification: Validate Saudi phone number, shift hours, and slot conflict,
     * check if customer exists in DB, and if found, log customer in and immediately create the booking.
     */
    public function verifyPhone(Request $request): JsonResponse
    {
        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->input('phone', ''));
        $request->merge([
            'phone' => $rawPhone,
            'booking_date' => (string) $request->input('booking_date', 'غداً - السبت'),
            'booking_time' => (string) $request->input('booking_time', '05:30 م'),
        ]);

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', new SaudiPhoneNumber($this->availabilityService)],
            'service_id' => ['required', 'string'],
            'specialist_id' => ['required', 'string'],
            'booking_date' => ['nullable', 'string'],
            'booking_time' => [
                'required',
                'string',
                new WithinSpecialistShift(availabilityService: $this->availabilityService),
                new AvailableSpecialistSlot(availabilityService: $this->availabilityService),
            ],
        ], [
            'phone.required' => 'يرجى إدخال رقم الجوال للمتابعة.',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة أولاً.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية أولاً.',
            'booking_time.required' => 'يرجى اختيار وقت الموعد أولاً.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);

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

        session(['logged_in_customer_id' => $customer->id]);

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
            'customer' => $this->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->getCustomerBookingsPayload($customer),
        ]);
    }

    /**
     * Redirect to Google OAuth 2.0 consent screen with booking state.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->query('phone', ''));
        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);

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
     * Handle Google OAuth 2.0 callback, validate shift & slot availability, create customer & booking in DB,
     * log customer in, and notify the opener window.
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

        $contextData = [
            'service_id' => (string) ($bookingContext['service_id'] ?? ''),
            'specialist_id' => (string) ($bookingContext['specialist_id'] ?? ''),
            'booking_date' => (string) ($bookingContext['booking_date'] ?? 'غداً - السبت'),
            'booking_time' => (string) ($bookingContext['booking_time'] ?? '05:30 م'),
        ];

        $slotValidator = Validator::make($contextData, [
            'booking_time' => [
                'required',
                'string',
                new WithinSpecialistShift(availabilityService: $this->availabilityService),
                new AvailableSpecialistSlot(availabilityService: $this->availabilityService),
            ],
        ]);

        if ($slotValidator->fails()) {
            return $this->renderPopupCallbackScript([
                'success' => false,
                'status' => 'validation_error',
                'message' => $slotValidator->errors()->first(),
            ]);
        }

        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone((string) ($bookingContext['phone'] ?? '0550000000'));
        $email = $googleUser['email'];
        $ownerName = $googleUser['name'] !== '' ? $googleUser['name'] : $this->extractNameFromEmail($email);
        $avatarUrl = $googleUser['picture'] ?? null;

        $customer = $this->findOrCreateGoogleCustomer($normalizedPhone, $email, $ownerName, $avatarUrl);

        session(['logged_in_customer_id' => $customer->id]);

        $bookingPayload = $this->createBookingForCustomer(
            customer: $customer,
            serviceCode: $contextData['service_id'],
            specialistSlug: $contextData['specialist_id'],
            dateLabel: $contextData['booking_date'],
            timeSlot: $contextData['booking_time']
        );

        session()->forget('pending_google_booking');

        return $this->renderPopupCallbackScript([
            'success' => true,
            'exists' => true,
            'registered_via_google' => true,
            'status' => 'confirmed',
            'message' => "تم التسجيل عبر Google وتأكيد الحجز باسم {$customer->name}",
            'customer' => $this->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->getCustomerBookingsPayload($customer),
        ]);
    }

    /**
     * Step 2 (AJAX): Register new customer via Google (using OAuth code, access_token, or Google profile),
     * validate shift and slot conflict via Rule classes, log customer in, and create the booking.
     */
    public function registerWithGoogle(Request $request): JsonResponse
    {
        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->input('phone', ''));
        $request->merge([
            'phone' => $rawPhone,
            'booking_date' => (string) $request->input('booking_date', 'غداً - السبت'),
            'booking_time' => (string) $request->input('booking_time', '05:30 م'),
        ]);

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
            'phone' => ['required', 'string', new SaudiPhoneNumber($this->availabilityService)],
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'service_id' => ['required', 'string'],
            'specialist_id' => ['required', 'string'],
            'booking_date' => ['nullable', 'string'],
            'booking_time' => [
                'required',
                'string',
                new WithinSpecialistShift(availabilityService: $this->availabilityService),
                new AvailableSpecialistSlot(availabilityService: $this->availabilityService),
            ],
        ], [
            'phone.required' => 'يرجى إدخال رقم الجوال السعودي.',
            'email.required' => 'تعذر الحصول على البريد الإلكتروني من حساب Google.',
            'email.email' => 'يرجى التأكد من صحة البريد الإلكتروني لحساب Google.',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية.',
            'booking_time.required' => 'يرجى اختيار وقت الموعد أولاً.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);
        $email = trim((string) $request->input('email'));
        $providedName = trim((string) $request->input('name', ''));
        $avatarUrl = $request->input('avatar_url');

        $ownerName = $providedName !== ''
            ? $providedName
            : $this->extractNameFromEmail($email);

        $customer = $this->findOrCreateGoogleCustomer(
            normalizedPhone: $normalizedPhone,
            email: $email,
            ownerName: $ownerName,
            avatarUrl: is_string($avatarUrl) ? $avatarUrl : null
        );

        session(['logged_in_customer_id' => $customer->id]);

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
            'customer' => $this->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->getCustomerBookingsPayload($customer),
        ]);
    }

    /**
     * Update the logged-in customer's profile (name, Saudi phone number, and optional avatar image).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $customerId = session('logged_in_customer_id') ?? $request->input('customer_id');
        $customer = $customerId ? Customer::find($customerId) : null;

        if (! $customer) {
            return response()->json([
                'success' => false,
                'status' => 'unauthenticated',
                'message' => 'يرجى تسجيل الدخول أو إتمام حجز أولاً لتعديل الملف الشخصي.',
            ], 401);
        }

        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->input('phone', ''));
        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);
        $request->merge(['phone' => $normalizedPhone]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => [
                'required',
                'string',
                new SaudiPhoneNumber($this->availabilityService),
                Rule::unique('customers', 'phone')->ignore($customer->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
        ], [
            'name.required' => 'يرجى إدخال الاسم الكامل.',
            'name.min' => 'يجب أن يتكون الاسم من حرفين على الأقل.',
            'phone.required' => 'يرجى إدخال رقم الجوال السعودي.',
            'phone.unique' => 'رقم الجوال هذا مسجل بالفعل لحساب آخر.',
            'avatar.image' => 'يرجى اختيار ملف صورة صالح.',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 4 ميجابايت.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $avatarUrl = $customer->avatar_url;
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('customers', 'public');
            $avatarUrl = asset('storage/'.$path);
        }

        $customer->update([
            'name' => trim((string) $request->input('name')),
            'phone' => $normalizedPhone,
            'avatar_url' => $avatarUrl,
        ]);

        session(['logged_in_customer_id' => $customer->id]);

        return response()->json([
            'success' => true,
            'status' => 'updated',
            'message' => 'تم تحديث بيانات ملفكِ الشخصي بنجاح.',
            'customer' => $this->formatCustomerPayload($customer->fresh()),
            'customer_bookings' => $this->getCustomerBookingsPayload($customer->fresh()),
        ]);
    }

    /**
     * Format Customer model into frontend profile array.
     *
     * @return array{id: int, customer_code: string, name: string, phone: string, email: ?string, avatar_url: string}
     */
    private function formatCustomerPayload(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'customer_code' => $customer->customer_code,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'avatar_url' => $customer->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=211C18&color=C5A373&bold=true',
        ];
    }

    /**
     * Return all bookings belonging to the given customer.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getCustomerBookingsPayload(Customer $customer): array
    {
        return Booking::with(['customer', 'items.service.category', 'items.specialist'])
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['confirmed', 'pending', 'in_progress', 'completed'])
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function (Booking $booking) use ($customer): array {
                $firstItem = $booking->items->first();
                $service = $firstItem?->service;
                $specialist = $firstItem?->specialist;

                $startTimeFormatted = '05:30 م';
                if ($booking->overall_start_time) {
                    $parsedTime = Carbon::parse($booking->overall_start_time);
                    $startTimeFormatted = $parsedTime->format('h:i').($parsedTime->format('A') === 'AM' ? ' ص' : ' م');
                }

                return [
                    'id' => $booking->booking_number,
                    'db_id' => $booking->id,
                    'customerName' => $customer->name,
                    'customerPhone' => $customer->phone,
                    'customerEmail' => $customer->email,
                    'categoryTitle' => $service?->category?->getTranslation('name', 'ar') ?? '',
                    'serviceName' => $service?->getTranslation('name', 'ar') ?? '',
                    'specialistName' => $specialist ? $specialist->getTranslation('name', 'ar') : '',
                    'date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '',
                    'time' => $startTimeFormatted,
                    'price' => ((int) $booking->total_amount).' ر.س',
                ];
            })
            ->values()
            ->all();
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
        $service = $this->availabilityService->resolveService($serviceCode)
            ?? Service::with('category')->firstOrFail();

        $specialist = $this->availabilityService->resolveSpecialist($specialistSlug)
            ?? Specialist::firstOrFail();

        $bookingDate = $this->availabilityService->resolveBookingDate($dateLabel);
        $startTime24 = $this->availabilityService->parseTimeTo24Hour($timeSlot) ?? '17:30:00';
        $durationMinutes = $this->availabilityService->resolveDurationMinutes($specialist, $service);
        $endTime24 = $this->availabilityService->calculateEndTime24($startTime24, $durationMinutes);

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
     * Extract a readable name from an email address if name was not explicitly entered.
     */
    private function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0] ?? 'عميلة هيرلاين';
        $cleaned = str_replace(['.', '_', '-'], ' ', $localPart);
        $cleaned = trim((string) preg_replace('/\d+/', '', $cleaned));

        return $cleaned !== '' ? mb_convert_case($cleaned, MB_CASE_TITLE, 'UTF-8') : $localPart;
    }
}
