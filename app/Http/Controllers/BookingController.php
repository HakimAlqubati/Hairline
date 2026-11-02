<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\SlotUnavailableException;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\MembershipTier;
use App\Rules\AvailableSpecialistSlot;
use App\Rules\NotPastDate;
use App\Rules\SaudiPhoneNumber;
use App\Rules\SpecialistOffersService;
use App\Rules\WithinSpecialistShift;
use App\Services\Booking\BookingCreationService;
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
use InvalidArgumentException;

class BookingController extends Controller
{
    /**
     * Regex pattern for valid Saudi mobile numbers (kept for reference compatibility).
     */
    public const SAUDI_PHONE_REGEX = SpecialistAvailabilityService::SAUDI_PHONE_REGEX;

    public function __construct(
        private readonly SpecialistAvailabilityService $availabilityService,
        private readonly BookingCreationService $bookingCreationService
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
        if ($service && ! $this->availabilityService->specialistOffersService($specialist, $service)) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'عذراً، الأخصائية المختارة لا تقدم هذه الخدمة.',
            ], 422);
        }

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
     * Step 1 of verification: Validate Saudi phone number and booking parameters.
     * If the customer already has an active authenticated session, create the booking immediately.
     * Otherwise, return a uniform response requiring Google verification without leaking customer existence or data.
     */
    public function verifyPhone(Request $request): JsonResponse
    {
        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->input('phone', ''));
        $request->merge(['phone' => $rawPhone]);

        $hasBooking = $request->filled('service_id') && $request->filled('specialist_id');

        $rules = [
            'phone' => ['required', 'string', new SaudiPhoneNumber($this->availabilityService)],
        ];

        if ($hasBooking) {
            $rules['service_id'] = ['required', 'string'];
            $rules['specialist_id'] = [
                'required',
                'string',
                new SpecialistOffersService($this->availabilityService),
            ];
            $rules['booking_date'] = [
                'required',
                'string',
                new NotPastDate($this->availabilityService),
            ];
            $rules['booking_time'] = [
                'required',
                'string',
                new WithinSpecialistShift(availabilityService: $this->availabilityService),
                new AvailableSpecialistSlot(availabilityService: $this->availabilityService),
            ];
        }

        $validator = Validator::make($request->all(), $rules, [
            'phone.required' => 'يرجى إدخال رقم الجوال للمتابعة.',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة أولاً.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية أولاً.',
            'booking_date.required' => 'يرجى اختيار تاريخ الموعد أولاً.',
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
        $loggedInCustomerId = session('logged_in_customer_id');
        $loggedInCustomer = $loggedInCustomerId ? Customer::find($loggedInCustomerId) : null;

        // If the customer is already authenticated in this session, verify phone matches their account
        if ($loggedInCustomer) {
            if ($rawPhone !== '' && $normalizedPhone !== $loggedInCustomer->phone && $rawPhone !== $loggedInCustomer->phone) {
                return response()->json([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => 'رقم الجوال المدخل لا يطابق رقم حسابكِ المسجل حالياً.',
                    'errors' => [
                        'phone' => ['رقم الجوال المدخل لا يطابق رقم حسابكِ المسجل حالياً.'],
                    ],
                ], 422);
            }
            $bookingPayload = null;
            if ($hasBooking) {
                try {
                    $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                        customer: $loggedInCustomer,
                        serviceCode: (string) $request->input('service_id'),
                        specialistSlug: (string) $request->input('specialist_id'),
                        dateLabel: (string) $request->input('booking_date'),
                        timeSlot: (string) $request->input('booking_time')
                    );
                } catch (SlotUnavailableException $e) {
                    return response()->json([
                        'success' => false,
                        'status' => 'slot_unavailable',
                        'message' => $e->getMessage(),
                    ], 409);
                } catch (InvalidArgumentException $e) {
                    return response()->json([
                        'success' => false,
                        'status' => 'validation_error',
                        'message' => $e->getMessage(),
                    ], 422);
                }
            }

            return response()->json([
                'success' => true,
                'exists' => true,
                'status' => 'confirmed',
                'message' => $hasBooking
                    ? "تم تأكيد الحجز بنجاح باسم {$loggedInCustomer->name}"
                    : "تم تسجيل الدخول بنجاح باسم {$loggedInCustomer->name}",
                'customer' => $this->formatCustomerPayload($loggedInCustomer),
                'booking' => $bookingPayload,
                'customer_bookings' => $this->getCustomerBookingsPayload($loggedInCustomer),
            ]);
        }

        // Return a uniform response to avoid phone enumeration and identity spoofing
        return response()->json([
            'success' => true,
            'exists' => false,
            'status' => 'needs_google_verification',
            'phone' => $normalizedPhone,
            'message' => 'يرجى المتابعة بحساب Google لإثبات هويتكِ وتأكيد الحجز.',
        ]);
    }

    /**
     * Redirect to Google OAuth 2.0 consent screen with state token and booking context preserved in session.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->query('phone', ''));
        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);

        $bookingContext = [
            'phone' => $normalizedPhone,
            'service_id' => (string) $request->query('service_id', ''),
            'specialist_id' => (string) $request->query('specialist_id', ''),
            'booking_date' => (string) $request->query('booking_date', ''),
            'booking_time' => (string) $request->query('booking_time', ''),
        ];

        $stateToken = bin2hex(random_bytes(16));
        session([
            'google_oauth_state' => $stateToken,
            'pending_google_booking' => $bookingContext,
        ]);

        $clientId = (string) config('services.google.client_id');
        $redirectUri = $this->resolveGoogleRedirectUri($request);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $stateToken,
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    /**
     * Handle Google OAuth 2.0 callback with state verification, validate shift & slot availability,
     * create or bind customer strictly, log customer in, and notify the opener window.
     */
    public function handleGoogleCallback(Request $request): Response
    {
        $code = (string) $request->query('code', '');
        $state = (string) $request->query('state', '');
        $sessionState = (string) session('google_oauth_state', '');

        // State verification to prevent CSRF and state tampering
        if ($state === '' || $sessionState === '' || ! hash_equals($sessionState, $state)) {
            return $this->renderPopupCallbackScript([
                'success' => false,
                'status' => 'oauth_error',
                'message' => 'جلسة التحقق غير صالحة أو انتهت مدتها، يرجى المحاولة مرة أخرى.',
            ]);
        }

        // Retrieve booking context strictly from server session
        $bookingContext = (array) session('pending_google_booking', []);
        session()->forget(['google_oauth_state', 'pending_google_booking']);

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

        $hasBooking = ! empty($bookingContext['service_id']) && ! empty($bookingContext['specialist_id']);

        if ($hasBooking) {
            $contextData = [
                'service_id' => (string) ($bookingContext['service_id'] ?? ''),
                'specialist_id' => (string) ($bookingContext['specialist_id'] ?? ''),
                'booking_date' => (string) ($bookingContext['booking_date'] ?? ''),
                'booking_time' => (string) ($bookingContext['booking_time'] ?? ''),
            ];

            $slotValidator = Validator::make($contextData, [
                'service_id' => ['required', 'string'],
                'specialist_id' => [
                    'required',
                    'string',
                    new SpecialistOffersService($this->availabilityService),
                ],
                'booking_date' => [
                    'required',
                    'string',
                    new NotPastDate($this->availabilityService),
                ],
                'booking_time' => [
                    'required',
                    'string',
                    new WithinSpecialistShift(availabilityService: $this->availabilityService),
                    new AvailableSpecialistSlot(availabilityService: $this->availabilityService),
                ],
            ], [
                'service_id.required' => 'يرجى اختيار الخدمة المطلوبة.',
                'specialist_id.required' => 'يرجى اختيار الأخصائية.',
                'booking_date.required' => 'يرجى اختيار تاريخ الموعد.',
                'booking_time.required' => 'يرجى اختيار وقت الموعد.',
            ]);

            if ($slotValidator->fails()) {
                return $this->renderPopupCallbackScript([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => $slotValidator->errors()->first(),
                ]);
            }
        }

        $rawPhone = (string) ($bookingContext['phone'] ?? '');
        $normalizedPhone = $rawPhone !== '' ? $this->availabilityService->normalizeSaudiPhone($rawPhone) : '';
        $email = $googleUser['email'];
        $ownerName = $googleUser['name'] !== '' ? $googleUser['name'] : $this->extractNameFromEmail($email);
        $avatarUrl = $googleUser['picture'] ?? null;

        try {
            $customer = $this->findOrCreateGoogleCustomer($normalizedPhone, $email, $ownerName, $avatarUrl);
        } catch (InvalidArgumentException $e) {
            return $this->renderPopupCallbackScript([
                'success' => false,
                'status' => 'validation_error',
                'message' => $e->getMessage(),
            ]);
        }

        $request->session()->regenerate();
        session(['logged_in_customer_id' => $customer->id]);

        $bookingPayload = null;
        if ($hasBooking) {
            try {
                $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                    customer: $customer,
                    serviceCode: $contextData['service_id'],
                    specialistSlug: $contextData['specialist_id'],
                    dateLabel: $contextData['booking_date'],
                    timeSlot: $contextData['booking_time']
                );
            } catch (SlotUnavailableException|InvalidArgumentException $e) {
                return $this->renderPopupCallbackScript([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $this->renderPopupCallbackScript([
            'success' => true,
            'exists' => true,
            'registered_via_google' => true,
            'status' => 'confirmed',
            'message' => $hasBooking
                ? "تم التسجيل عبر Google وتأكيد الحجز باسم {$customer->name}"
                : "تم تسجيل الدخول عبر Google بنجاح مرحباً بكِ {$customer->name}",
            'customer' => $this->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->getCustomerBookingsPayload($customer),
        ]);
    }

    /**
     * Step 2 (AJAX): Register or authenticate customer strictly via Google OAuth code.
     * Identity data (email, name, picture) is obtained exclusively from Google, never from request payload.
     */
    public function registerWithGoogle(Request $request): JsonResponse
    {
        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->input('phone', ''));
        $request->merge(['phone' => $rawPhone]);

        $oauthCode = trim((string) $request->input('code', ''));
        if ($oauthCode === '') {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'رمز التحقق من حساب Google مطلوب لإتمام العملية.',
            ], 422);
        }

        $googleUser = $this->exchangeGoogleCodeForUser($oauthCode, 'postmessage');
        if (! $googleUser) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'تعذر التحقق من حساب Google، يرجى المحاولة مرة أخرى.',
            ], 422);
        }

        $hasBooking = $request->filled('service_id') && $request->filled('specialist_id');

        $rules = [
            'phone' => ['required', 'string', new SaudiPhoneNumber($this->availabilityService)],
        ];

        if ($hasBooking) {
            $rules['service_id'] = ['required', 'string'];
            $rules['specialist_id'] = [
                'required',
                'string',
                new SpecialistOffersService($this->availabilityService),
            ];
            $rules['booking_date'] = [
                'required',
                'string',
                new NotPastDate($this->availabilityService),
            ];
            $rules['booking_time'] = [
                'required',
                'string',
                new WithinSpecialistShift(availabilityService: $this->availabilityService),
                new AvailableSpecialistSlot(availabilityService: $this->availabilityService),
            ];
        }

        $validator = Validator::make($request->all(), $rules, [
            'phone.required' => 'يرجى إدخال رقم الجوال.',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة أولاً.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية أولاً.',
            'booking_date.required' => 'يرجى اختيار تاريخ الموعد أولاً.',
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

        // Prevent cross-account contamination when already authenticated
        $loggedInCustomerId = session('logged_in_customer_id');
        if ($loggedInCustomerId) {
            $loggedInCustomer = Customer::find($loggedInCustomerId);
            if ($loggedInCustomer && $normalizedPhone !== '' && $normalizedPhone !== $loggedInCustomer->phone) {
                return response()->json([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => 'أنتِ مسجلة دخولكِ بالفعل بحساب آخر. يرجى تسجيل الخروج أولاً لتسجيل حساب جديد.',
                ], 422);
            }
        }
        $email = $googleUser['email'];
        $ownerName = $googleUser['name'];
        $avatarUrl = $googleUser['picture'] ?? null;

        try {
            $customer = $this->findOrCreateGoogleCustomer(
                normalizedPhone: $normalizedPhone,
                email: $email,
                ownerName: $ownerName,
                avatarUrl: $avatarUrl
            );
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $e->getMessage(),
            ], 422);
        }

        $request->session()->regenerate();
        session(['logged_in_customer_id' => $customer->id]);

        $bookingPayload = null;
        if ($hasBooking) {
            try {
                $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                    customer: $customer,
                    serviceCode: (string) $request->input('service_id'),
                    specialistSlug: (string) $request->input('specialist_id'),
                    dateLabel: (string) $request->input('booking_date'),
                    timeSlot: (string) $request->input('booking_time')
                );
            } catch (SlotUnavailableException $e) {
                return response()->json([
                    'success' => false,
                    'status' => 'slot_unavailable',
                    'message' => $e->getMessage(),
                ], 409);
            } catch (InvalidArgumentException $e) {
                return response()->json([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'exists' => true,
            'registered_via_google' => true,
            'status' => 'confirmed',
            'message' => $hasBooking
                ? "تم التسجيل عبر Google وتأكيد الحجز باسم {$customer->name}"
                : "تم تسجيل الدخول عبر Google بنجاح مرحباً بكِ {$customer->name}",
            'customer' => $this->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->getCustomerBookingsPayload($customer),
        ]);
    }

    /**
     * Update the logged-in customer's profile (name, Saudi phone number, and optional avatar image).
     * Protected strictly by active session to prevent modifying other customers' records.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $customerId = session('logged_in_customer_id');
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
            'phone.required' => 'يرجى إدخال رقم الجوال.',
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
     * Log out the currently authenticated customer from the session.
     */
    public function logout(Request $request): JsonResponse
    {
        session()->forget('logged_in_customer_id');
        session()->forget('pending_google_booking');
        session()->forget('google_oauth_state');

        return response()->json([
            'success' => true,
            'status' => 'logged_out',
            'message' => 'تم تسجيل الخروج بنجاح.',
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
     * Find or create the customer in the database after Google verification with strict account binding.
     *
     * @throws InvalidArgumentException
     */
    private function findOrCreateGoogleCustomer(
        string $normalizedPhone,
        string $email,
        string $ownerName,
        ?string $avatarUrl = null
    ): Customer {
        if ($normalizedPhone === '') {
            throw new InvalidArgumentException('رقم الجوال السعودي مطلوب لإتمام التسجيل.');
        }

        $emailTrimmed = strtolower(trim($email));
        if ($emailTrimmed === '') {
            throw new InvalidArgumentException('البريد الإلكتروني لحساب Google غير متوفر.');
        }

        return DB::transaction(function () use ($normalizedPhone, $emailTrimmed, $ownerName, $avatarUrl): Customer {
            $customerByPhone = Customer::where('phone', $normalizedPhone)->first();
            $customerByEmail = Customer::where('email', $emailTrimmed)->first();

            if ($customerByPhone) {
                // If customer has an email registered, it must match Google's verified email
                if (! empty($customerByPhone->email)) {
                    if (strtolower(trim((string) $customerByPhone->email)) !== $emailTrimmed) {
                        throw new InvalidArgumentException('حساب Google لا يطابق البريد الإلكتروني المسجل لهذا الرقم.');
                    }
                } else {
                    // Customer exists by phone but has no email linked
                    throw new InvalidArgumentException('رقم الجوال مسجل مسبقاً ولكن غير مرتبط ببريد إلكتروني، يرجى التواصل مع إدارة الصالون لربط البريد بحسابكِ.');
                }

                // If phone and email match, update avatar if currently null
                if ($avatarUrl && empty($customerByPhone->avatar_url)) {
                    $customerByPhone->update(['avatar_url' => $avatarUrl]);
                }

                return $customerByPhone;
            }

            // Phone is NOT registered yet.
            // Check if the Google email is already linked to another customer record with a different phone:
            if ($customerByEmail) {
                throw new InvalidArgumentException('البريد الإلكتروني لحساب Google مرتبط بالفعل برقم جوال آخر في صالون هيرلاين.');
            }

            // Neither phone nor email exists -> Register new verified customer
            $defaultTierId = MembershipTier::where('slug', 'silver')->value('id')
                ?? MembershipTier::orderBy('min_points_required')->value('id');

            do {
                $customerCode = 'HL-'.random_int(1000, 9999);
            } while (Customer::where('customer_code', $customerCode)->exists());

            return Customer::create([
                'membership_tier_id' => $defaultTierId,
                'customer_code' => $customerCode,
                'name' => $ownerName !== '' ? $ownerName : $this->extractNameFromEmail($emailTrimmed),
                'phone' => $normalizedPhone,
                'email' => $emailTrimmed,
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
     * Fetch Google user profile from access token and verify that email is verified.
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
        $emailVerified = (bool) $userResponse->json('email_verified', false);
        if ($email === '' || ! $emailVerified) {
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
                }, window.location.origin);
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
