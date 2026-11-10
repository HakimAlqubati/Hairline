<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Exceptions\SlotUnavailableException;
use App\Http\Requests\Booking\ConfirmGuestBookingRequest;
use App\Http\Requests\Booking\GetAvailabilityRequest;
use App\Http\Requests\Booking\SendOtpRequest;
use App\Http\Requests\Booking\VerifyOtpAndBookRequest;
use App\Http\Requests\Booking\VerifyPhoneRequest;
use App\Models\Booking;
use App\Services\Booking\BookingCreationService;
use App\Services\Booking\BookingPayloadFormatter;
use App\Services\Booking\SpecialistAvailabilityService;
use App\Services\Customer\CustomerRegistrationService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class BookingController extends Controller
{
    /**
     * Regex pattern for valid Saudi mobile numbers (kept for reference compatibility).
     */
    public const SAUDI_PHONE_REGEX = SpecialistAvailabilityService::SAUDI_PHONE_REGEX;

    public function __construct(
        private readonly SpecialistAvailabilityService $availabilityService,
        private readonly BookingCreationService $bookingCreationService,
        private readonly BookingPayloadFormatter $payloadFormatter,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CustomerRegistrationService $customerRegistrationService
    ) {}

    /**
     * Return shift-based time slots and real-time database booking conflicts for a specialist and date.
     */
    public function getAvailability(GetAvailabilityRequest $request): JsonResponse
    {
        $itemsInput = $request->input('items') ?: $request->query('items');
        if (is_string($itemsInput)) {
            $itemsInput = json_decode($itemsInput, true);
        }

        if (! empty($itemsInput) && is_array($itemsInput)) {
            $resolvedItems = $this->availabilityService->resolveBookingItems(null, null, $itemsInput);
            if (empty($resolvedItems)) {
                return response()->json([
                    'success' => false,
                    'status' => 'not_found',
                    'message' => 'الخدمات أو الأخصائيات المختارة غير متاحة حالياً.',
                ], 404);
            }

            foreach ($resolvedItems as $it) {
                if (! $this->availabilityService->specialistOffersService($it['specialist'], $it['service'])) {
                    $spName = $it['specialist']->getTranslation('name', 'ar');
                    $srvName = $it['service']->getTranslation('name', 'ar');

                    return response()->json([
                        'success' => false,
                        'status' => 'validation_error',
                        'message' => "عذراً، الأخصائية ({$spName}) لا تقدم خدمة ({$srvName}).",
                    ], 422);
                }
            }

            $date = $this->availabilityService->resolveBookingDate((string) $request->query('booking_date', ''));
            $availability = $this->availabilityService->getAvailabilityForItems($resolvedItems, $date);

            return response()->json([
                'success' => true,
                ...$availability,
            ]);
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
     * Step 1 of Customer Registration & Booking:
     * Validate customer name, Saudi mobile phone, and selected slot availability,
     * then dispatch/simulate a 4-digit SMS OTP code (defaults to 1100 in simulation mode).
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $rawPhone = (string) $request->input('phone', '');
        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);
        $customerName = trim((string) $request->input('name', ''));

        try {
            $otpResult = $this->customerRegistrationService->initiateRegistrationOtp(
                normalizedPhone: $normalizedPhone,
                customerName: $customerName
            );
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $e->getMessage(),
                'errors' => [
                    'name' => [$e->getMessage()],
                ],
            ], 422);
        }

        if (! $otpResult['sent']) {
            return response()->json([
                'success' => false,
                'status' => $otpResult['status'],
                'message' => $otpResult['message'],
                'resend_after' => $otpResult['resend_after'],
            ], 429);
        }

        return response()->json([
            'success' => true,
            'status' => 'otp_sent',
            'channel' => $otpResult['channel'] ?? 'whatsapp',
            'channel_label' => $otpResult['channel_label'] ?? 'واتساب',
            'message' => $otpResult['message'],
            'phone' => $normalizedPhone,
            'masked_phone' => $otpResult['masked_phone'],
            'name' => $customerName,
            'expires_in' => $otpResult['expires_in'],
            'resend_after' => $otpResult['resend_after'],
            'is_simulation' => $otpResult['is_simulation'],
            'simulated_code' => $otpResult['simulated_code'],
        ]);
    }

    /**
     * Step 2 of Customer Registration & Booking:
     * Check and verify the 4-digit SMS OTP code, register the customer via CustomerRepositoryInterface,
     * authenticate their session, and create the confirmed booking as a registered customer.
     */
    public function verifyOtpAndConfirmBooking(VerifyOtpAndBookRequest $request): JsonResponse
    {
        $rawPhone = (string) $request->input('phone', '');
        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);
        $customerName = trim((string) $request->input('name', ''));
        $otpCode = (string) $request->input('otp_code', '');

        $verification = $this->customerRegistrationService->verifyOtpAndRegisterCustomer(
            normalizedPhone: $normalizedPhone,
            otpCode: $otpCode,
            fallbackName: $customerName
        );

        if (! $verification['verified'] || ! $verification['customer']) {
            return response()->json([
                'success' => false,
                'status' => $verification['status'],
                'message' => $verification['message'],
                'remaining_attempts' => $verification['remaining_attempts'],
                'errors' => [
                    'otp_code' => [$verification['message']],
                ],
            ], 422);
        }

        $customer = $verification['customer'];

        $request->session()->regenerate();
        session(['logged_in_customer_id' => $customer->id]);

        $hasBooking = ($request->filled('service_id') && $request->filled('specialist_id')) || $request->filled('items');
        $bookingPayload = null;

        if ($hasBooking) {
            $serviceInput = $request->input('service_ids') ?: $request->input('service_id');
            $guestsCount = $request->has('guests_count') ? (int) $request->input('guests_count') : 1;
            $customerNotes = $request->filled('customer_notes') ? (string) $request->input('customer_notes') : null;

            try {
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
            'status' => 'confirmed',
            'customer_type' => Booking::CUSTOMER_TYPE_REGISTERED,
            'message' => $hasBooking
                ? "تم تأكيد الرمز وتسجيل حجزكِ بنجاح باسم {$customer->name}"
                : "تم تأكيد الرمز وتسجيل حسابكِ بنجاح باسم {$customer->name}",
            'customer' => $this->payloadFormatter->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->payloadFormatter->getCustomerBookingsPayload($customer),
        ]);
    }

    /**
     * Step 1 of verification: Validate Saudi phone number and booking parameters.
     * If the customer already has an active authenticated session, create the booking immediately.
     */
    public function verifyPhone(VerifyPhoneRequest $request): JsonResponse
    {
        $rawPhone = (string) $request->input('phone', '');
        $hasBooking = ($request->filled('service_id') && $request->filled('specialist_id')) || $request->filled('items');

        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);
        $loggedInCustomerId = session('logged_in_customer_id');
        $loggedInCustomer = $loggedInCustomerId ? $this->customerRepository->findById((int) $loggedInCustomerId) : null;

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
                    $serviceInput = $request->input('service_ids') ?: $request->input('service_id');
                    $guestsCount = $request->has('guests_count') ? (int) $request->input('guests_count') : 1;
                    $customerNotes = $request->filled('customer_notes') ? (string) $request->input('customer_notes') : null;

                    $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                        customer: $loggedInCustomer,
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
                'status' => 'confirmed',
                'message' => $hasBooking
                    ? "تم تأكيد الحجز بنجاح باسم {$loggedInCustomer->name}"
                    : "تم تسجيل الدخول بنجاح باسم {$loggedInCustomer->name}",
                'customer' => $this->payloadFormatter->formatCustomerPayload($loggedInCustomer),
                'booking' => $bookingPayload,
                'customer_bookings' => $this->payloadFormatter->getCustomerBookingsPayload($loggedInCustomer),
            ]);
        }

        $existingCustomer = $this->customerRepository->findByPhone($normalizedPhone);
        if ($existingCustomer) {
            return response()->json([
                'success' => true,
                'exists' => true,
                'status' => 'customer_found',
                'customer_name' => $existingCustomer->name,
                'phone' => $normalizedPhone,
                'message' => "أهلاً بكِ مجدداً يا {$existingCustomer->name}!",
            ]);
        }

        return response()->json([
            'success' => true,
            'exists' => false,
            'status' => 'new_customer',
            'phone' => $normalizedPhone,
            'message' => 'أهلاً بكِ في هيرلاين! يرجى إدخال اسمكِ الكريم للمتابعة.',
        ]);
    }

    /**
     * Legacy fallback endpoint for direct booking confirmation.
     */
    public function confirmGuestBooking(ConfirmGuestBookingRequest $request): JsonResponse
    {
        $rawPhone = (string) $request->input('phone', '');
        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);
        $customer = $this->customerRepository->findByPhone($normalizedPhone);

        if (! $customer) {
            $name = trim((string) $request->input('name', ''));
            if ($name === '' || mb_strlen($name) < 2) {
                return response()->json([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => 'يرجى إدخال اسمكِ الكريم لإتمام الحجز.',
                    'errors' => ['name' => ['يرجى إدخال اسمكِ الكريم لإتمام الحجز.']],
                ], 422);
            }

            $customer = $this->customerRepository->registerOrUpdateByPhone($normalizedPhone, $name);
        }

        $request->session()->regenerate();
        session(['logged_in_customer_id' => $customer->id]);

        $serviceInput = $request->input('service_ids') ?: $request->input('service_id');
        $guestsCount = $request->has('guests_count') ? (int) $request->input('guests_count') : 1;
        $customerNotes = $request->filled('customer_notes') ? (string) $request->input('customer_notes') : null;

        try {
            $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                customer: $customer,
                serviceCode: $serviceInput,
                specialistSlug: (string) $request->input('specialist_id', ''),
                dateLabel: (string) $request->input('booking_date'),
                timeSlot: (string) $request->input('booking_time'),
                customerType: Booking::CUSTOMER_TYPE_GUEST,
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

        return response()->json([
            'success' => true,
            'status' => 'confirmed',
            'customer_type' => Booking::CUSTOMER_TYPE_GUEST,
            'message' => "تم تأكيد حجزكِ بنجاح باسم {$customer->name}",
            'customer' => $this->payloadFormatter->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->payloadFormatter->getCustomerBookingsPayload($customer),
        ]);
    }
}
