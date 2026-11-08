<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\RegisterWithGoogleRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Rules\AvailableSpecialistSlot;
use App\Rules\NotPastDate;
use App\Rules\SpecialistOffersService;
use App\Rules\WithinSpecialistShift;
use App\Services\Auth\GoogleCustomerSyncService;
use App\Services\Auth\GoogleOAuthService;
use App\Services\Booking\BookingCreationService;
use App\Services\Booking\BookingPayloadFormatter;
use App\Services\Booking\SpecialistAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class GoogleAuthController extends Controller
{
    public function __construct(
        private readonly GoogleOAuthService $googleOAuthService,
        private readonly GoogleCustomerSyncService $googleCustomerSyncService,
        private readonly SpecialistAvailabilityService $availabilityService,
        private readonly BookingCreationService $bookingCreationService,
        private readonly BookingPayloadFormatter $payloadFormatter
    ) {}

    /**
     * Redirect to Google OAuth 2.0 consent screen with state token and booking context preserved in session.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $authUrl = $this->googleOAuthService->buildAuthorizationUrl($request);

        return redirect()->away($authUrl);
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
            return $this->googleOAuthService->renderPopupCallbackScript([
                'success' => false,
                'status' => 'oauth_error',
                'message' => 'جلسة التحقق غير صالحة أو انتهت مدتها، يرجى المحاولة مرة أخرى.',
            ]);
        }

        // Retrieve booking context strictly from server session
        $bookingContext = (array) session('pending_google_booking', []);
        session()->forget(['google_oauth_state', 'pending_google_booking']);

        if ($code === '') {
            return $this->googleOAuthService->renderPopupCallbackScript([
                'success' => false,
                'status' => 'oauth_cancelled',
                'message' => 'تم إلغاء عملية تسجيل الدخول عبر Google.',
            ]);
        }

        $redirectUri = $this->googleOAuthService->resolveGoogleRedirectUri($request);
        $googleUser = $this->googleOAuthService->exchangeGoogleCodeForUser($code, $redirectUri);

        if (! $googleUser) {
            return $this->googleOAuthService->renderPopupCallbackScript([
                'success' => false,
                'status' => 'oauth_error',
                'message' => 'تعذر التحقق من حساب Google، يرجى المحاولة مرة أخرى.',
            ]);
        }

        $isProfile = (bool) ($bookingContext['is_profile'] ?? false);
        $hasBooking = ! $isProfile && ((! empty($bookingContext['service_id']) && ! empty($bookingContext['specialist_id'])) || ! empty($bookingContext['items']));

        if ($hasBooking) {
            $contextData = [
                'service_id' => (string) ($bookingContext['service_id'] ?? ''),
                'specialist_id' => (string) ($bookingContext['specialist_id'] ?? ''),
                'booking_date' => (string) ($bookingContext['booking_date'] ?? ''),
                'booking_time' => (string) ($bookingContext['booking_time'] ?? ''),
                'guests_count' => ! empty($bookingContext['guests_count']) ? (int) $bookingContext['guests_count'] : 1,
                'customer_notes' => ! empty($bookingContext['customer_notes']) ? (string) $bookingContext['customer_notes'] : null,
                'items' => ! empty($bookingContext['items']) ? (array) $bookingContext['items'] : null,
            ];

            $slotValidator = Validator::make($contextData, [
                'service_id' => ['required_without:items', 'nullable', 'string'],
                'specialist_id' => [
                    'required_without:items',
                    'nullable',
                    'string',
                    new SpecialistOffersService($this->availabilityService),
                ],
                'items' => ['nullable', 'array'],
                'items.*.service_id' => ['required_with:items', 'string'],
                'items.*.specialist_id' => ['required_with:items', 'string', new SpecialistOffersService($this->availabilityService)],
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
                'service_id.required_without' => 'يرجى اختيار الخدمة المطلوبة.',
                'specialist_id.required_without' => 'يرجى اختيار الأخصائية.',
                'booking_date.required' => 'يرجى اختيار تاريخ الموعد.',
                'booking_time.required' => 'يرجى اختيار وقت الموعد.',
            ]);

            if ($slotValidator->fails()) {
                return $this->googleOAuthService->renderPopupCallbackScript([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => $slotValidator->errors()->first(),
                ]);
            }
        }

        $rawPhone = (string) ($bookingContext['phone'] ?? '');
        $normalizedPhone = $rawPhone !== '' ? $this->availabilityService->normalizeSaudiPhone($rawPhone) : '';
        $email = $googleUser['email'];
        $ownerName = $googleUser['name'] !== '' ? $googleUser['name'] : $this->googleOAuthService->extractNameFromEmail($email);
        $avatarUrl = $googleUser['picture'] ?? null;

        try {
            $customer = $this->googleCustomerSyncService->findOrCreateGoogleCustomer($normalizedPhone, $email, $ownerName, $avatarUrl);
        } catch (InvalidArgumentException $e) {
            return $this->googleOAuthService->renderPopupCallbackScript([
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
                $serviceInput = $contextData['service_ids'] ?? $contextData['service_id'];
                $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                    customer: $customer,
                    serviceCode: $serviceInput,
                    specialistSlug: $contextData['specialist_id'],
                    dateLabel: $contextData['booking_date'],
                    timeSlot: $contextData['booking_time'],
                    customerType: Booking::CUSTOMER_TYPE_REGISTERED,
                    guestsCount: $contextData['guests_count'] ?? 1,
                    customerNotes: $contextData['customer_notes'] ?? null,
                    items: $contextData['items'] ?? null
                );
            } catch (SlotUnavailableException|InvalidArgumentException $e) {
                return $this->googleOAuthService->renderPopupCallbackScript([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $this->googleOAuthService->renderPopupCallbackScript([
            'success' => true,
            'exists' => true,
            'registered_via_google' => true,
            'status' => 'confirmed',
            'message' => $hasBooking
                ? "تم التسجيل عبر Google وتأكيد الحجز باسم {$customer->name}"
                : "تم تسجيل الدخول عبر Google بنجاح مرحباً بكِ {$customer->name}",
            'customer' => $this->payloadFormatter->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->payloadFormatter->getCustomerBookingsPayload($customer),
        ]);
    }

    /**
     * Step 2 (AJAX): Register or authenticate customer strictly via Google OAuth code.
     * Identity data (email, name, picture) is obtained exclusively from Google, never from request payload.
     */
    public function registerWithGoogle(RegisterWithGoogleRequest $request): JsonResponse
    {
        $oauthCode = trim((string) $request->input('code', ''));

        $googleUser = $this->googleOAuthService->exchangeGoogleCodeForUser($oauthCode, 'postmessage');
        if (! $googleUser) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'تعذر التحقق من حساب Google، يرجى المحاولة مرة أخرى.',
            ], 422);
        }

        $isProfile = $request->boolean('is_profile');
        $hasBooking = ! $isProfile && (($request->filled('service_id') && $request->filled('specialist_id')) || $request->filled('items'));

        $rawPhone = (string) $request->input('phone', '');
        $normalizedPhone = $rawPhone !== '' ? $this->availabilityService->normalizeSaudiPhone($rawPhone) : '';

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
            $customer = $this->googleCustomerSyncService->findOrCreateGoogleCustomer(
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
                $serviceInput = $request->input('service_ids') ?: $request->input('service_id');
                $guestsCount = $request->has('guests_count') ? (int) $request->input('guests_count') : 1;
                $customerNotes = $request->filled('customer_notes') ? (string) $request->input('customer_notes') : null;

                $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                    customer: $customer,
                    serviceCode: $serviceInput,
                    specialistSlug: (string) $request->input('specialist_id', ''),
                    dateLabel: (string) $request->input('booking_date'),
                    timeSlot: (string) $request->input('booking_time'),
                    customerType: Booking::CUSTOMER_TYPE_REGISTERED,
                    guestsCount: $guestsCount,
                    customerNotes: $customerNotes,
                    items: $request->input('items')
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
            'customer' => $this->payloadFormatter->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->payloadFormatter->getCustomerBookingsPayload($customer),
        ]);
    }
}
