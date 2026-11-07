<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\SlotUnavailableException;
use App\Http\Requests\Booking\ConfirmGuestBookingRequest;
use App\Http\Requests\Booking\GetAvailabilityRequest;
use App\Http\Requests\Booking\VerifyPhoneRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\MembershipTier;
use App\Services\Booking\BookingCreationService;
use App\Services\Booking\BookingPayloadFormatter;
use App\Services\Booking\SpecialistAvailabilityService;
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
        private readonly BookingPayloadFormatter $payloadFormatter
    ) {}

    /**
     * Return shift-based time slots and real-time database booking conflicts for a specialist and date.
     */
    public function getAvailability(GetAvailabilityRequest $request): JsonResponse
    {
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
    public function verifyPhone(VerifyPhoneRequest $request): JsonResponse
    {
        $rawPhone = (string) $request->input('phone', '');
        $hasBooking = $request->filled('service_id') && $request->filled('specialist_id');

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
                    $serviceInput = $request->input('service_ids') ?: $request->input('service_id');
                    $guestsCount = $request->has('guests_count') ? (int) $request->input('guests_count') : 1;
                    $customerNotes = $request->filled('customer_notes') ? (string) $request->input('customer_notes') : null;

                    $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                        customer: $loggedInCustomer,
                        serviceCode: $serviceInput,
                        specialistSlug: (string) $request->input('specialist_id'),
                        dateLabel: (string) $request->input('booking_date'),
                        timeSlot: (string) $request->input('booking_time'),
                        customerType: Booking::CUSTOMER_TYPE_REGISTERED,
                        guestsCount: $guestsCount,
                        customerNotes: $customerNotes
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

        $existingCustomer = Customer::where('phone', $normalizedPhone)->first();
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
     * Confirm booking as a guest (or returning customer without mandatory Google sign-in).
     * Creates or fetches customer, logs them in automatically, and records customer_type = 'guest'.
     */
    public function confirmGuestBooking(ConfirmGuestBookingRequest $request): JsonResponse
    {
        $rawPhone = (string) $request->input('phone', '');
        $normalizedPhone = $this->availabilityService->normalizeSaudiPhone($rawPhone);
        $customer = Customer::where('phone', $normalizedPhone)->first();

        if (! $customer) {
            $name = trim((string) $request->input('name', ''));
            if ($name === '' || mb_strlen($name) < 2) {
                return response()->json([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => 'يرجى إدخال اسمكِ الكريم لإتمام الحجز كـ زائر.',
                    'errors' => ['name' => ['يرجى إدخال اسمكِ الكريم لإتمام الحجز كـ زائر.']],
                ], 422);
            }

            $defaultTierId = MembershipTier::where('slug', 'silver')->value('id')
                ?? MembershipTier::orderBy('min_points_required')->value('id');

            do {
                $customerCode = 'HL-'.random_int(1000, 9999);
            } while (Customer::where('customer_code', $customerCode)->exists());

            $customer = Customer::create([
                'membership_tier_id' => $defaultTierId,
                'customer_code' => $customerCode,
                'name' => $name,
                'phone' => $normalizedPhone,
                'email' => null,
                'avatar_url' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=FFFFFF&color=C29E6D&bold=true',
                'total_points' => 0,
                'is_active' => true,
            ]);
        }

        // Automatically log the customer in
        $request->session()->regenerate();
        session(['logged_in_customer_id' => $customer->id]);

        $serviceInput = $request->input('service_ids') ?: $request->input('service_id');
        $guestsCount = $request->has('guests_count') ? (int) $request->input('guests_count') : 1;
        $customerNotes = $request->filled('customer_notes') ? (string) $request->input('customer_notes') : null;

        try {
            $bookingPayload = $this->bookingCreationService->createBookingForCustomer(
                customer: $customer,
                serviceCode: $serviceInput,
                specialistSlug: (string) $request->input('specialist_id'),
                dateLabel: (string) $request->input('booking_date'),
                timeSlot: (string) $request->input('booking_time'),
                customerType: Booking::CUSTOMER_TYPE_GUEST,
                guestsCount: $guestsCount,
                customerNotes: $customerNotes
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
            'message' => "تم تأكيد حجزكِ بنجاح كـ زائر باسم {$customer->name}",
            'customer' => $this->payloadFormatter->formatCustomerPayload($customer),
            'booking' => $bookingPayload,
            'customer_bookings' => $this->payloadFormatter->getCustomerBookingsPayload($customer),
        ]);
    }
}
