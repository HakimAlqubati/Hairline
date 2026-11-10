<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Exceptions\SlotUnavailableException;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Specialist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingCreationService
{
    public function __construct(
        private readonly SpecialistAvailabilityService $availabilityService
    ) {}

    /**
     * Persist Booking and BookingItem records directly in the database with concurrency locking
     * and immediate re-validation of slot availability.
     *
     * @param  array<int, array{service_id: string|int, specialist_id: string|int}>|null  $items
     * @return array{
     *     id: string,
     *     db_id: int,
     *     status: string,
     *     customerName: string,
     *     customerPhone: string,
     *     customerEmail: ?string,
     *     categoryTitle: string,
     *     serviceName: string,
     *     specialistName: string,
     *     date: string,
     *     bookingDateIso: string,
     *     time: string,
     *     price: string,
     *     guests_count: int,
     *     customer_notes: ?string,
     *     items: array<int, array{serviceName: string, specialistName: string, startTime: string, endTime: string, durationMinutes: int}>
     * }
     *
     * @throws InvalidArgumentException
     * @throws SlotUnavailableException
     */
    public function createBookingForCustomer(
        Customer $customer,
        string|array|null $serviceCode = null,
        string|array|null $specialistSlug = null,
        string $dateLabel = '',
        string $timeSlot = '',
        string $customerType = Booking::CUSTOMER_TYPE_REGISTERED,
        int $guestsCount = 1,
        ?string $customerNotes = null,
        ?array $items = null
    ): array {
        $resolvedItems = $this->availabilityService->resolveBookingItems(
            serviceInput: $serviceCode,
            specialistInput: is_string($specialistSlug) ? $specialistSlug : null,
            itemsInput: $items
        );

        if (empty($resolvedItems)) {
            throw new InvalidArgumentException('الخدمات أو الأخصائيات المختارة غير متاحة.');
        }

        foreach ($resolvedItems as $item) {
            if (! $this->availabilityService->specialistOffersService($item['specialist'], $item['service'])) {
                $spName = $item['specialist']->getTranslation('name', 'ar');
                $srvName = $item['service']->getTranslation('name', 'ar');
                throw new InvalidArgumentException("عذراً، الأخصائية ({$spName}) لا تقدم خدمة ({$srvName}).");
            }
        }

        $timeSlotClean = trim($timeSlot);
        if ($timeSlotClean === '') {
            throw new InvalidArgumentException('يرجى اختيار وقت الموعد.');
        }

        $startTime24 = $this->availabilityService->parseTimeTo24Hour($timeSlotClean);
        if ($startTime24 === null) {
            throw new InvalidArgumentException('صيغة وقت الموعد المختار غير صالحة.');
        }

        $dateLabelClean = trim($dateLabel);
        if ($dateLabelClean === '') {
            throw new InvalidArgumentException('يرجى اختيار تاريخ الموعد.');
        }

        $bookingDate = $this->availabilityService->resolveBookingDate($dateLabelClean);

        $today = Carbon::today('Asia/Riyadh');
        if ($bookingDate->isBefore($today)) {
            throw new InvalidArgumentException('لا يمكن حجز موعد في تاريخ سابق.');
        }

        $now = Carbon::now('Asia/Riyadh');
        if ($bookingDate->isSameDay($today)) {
            $slotMinutes = $this->availabilityService->timeToMinutes($startTime24);
            $currentMinutes = ($now->hour * 60) + $now->minute;
            if ($slotMinutes < ($currentMinutes + 15)) {
                throw new InvalidArgumentException('عذراً، هذا الموعد قد مضى وقته لليوم. يرجى اختيار موعد قادم.');
            }
        }

        $hasExplicitItemTimes = false;
        if (is_array($items)) {
            foreach ($items as $it) {
                if (! empty($it['booking_time'])) {
                    $hasExplicitItemTimes = true;
                    break;
                }
            }
        }

        $specialistCursors = [];
        $timeline = [];
        $totalDurationMinutes = 0;
        $allStartTimes = [];
        $allEndTimes = [];
        $cursorTime = $startTime24;

        foreach ($resolvedItems as $idx => $item) {
            $sp = $item['specialist'];
            $duration = $item['duration_minutes'];

            if ($hasExplicitItemTimes) {
                $explicitTime = null;
                if (! empty($items[$idx]['booking_time'])) {
                    $explicitTime = $this->availabilityService->parseTimeTo24Hour((string) $items[$idx]['booking_time']);
                }

                if ($explicitTime !== null) {
                    $itemStart24 = $explicitTime;
                } elseif (isset($specialistCursors[$sp->id])) {
                    $itemStart24 = $specialistCursors[$sp->id];
                } else {
                    $itemStart24 = $startTime24;
                }
            } else {
                $itemStart24 = $cursorTime;
            }

            if ($bookingDate->isSameDay($today)) {
                $slotMinutes = $this->availabilityService->timeToMinutes($itemStart24);
                $currentMinutes = ($now->hour * 60) + $now->minute;
                if ($slotMinutes < ($currentMinutes + 15)) {
                    throw new InvalidArgumentException('عذراً، هذا الموعد قد مضى وقته لليوم. يرجى اختيار موعد قادم.');
                }
            }

            $shiftCheck = $this->availabilityService->validateWithinShift($sp, $bookingDate, $itemStart24, $duration);
            if (! $shiftCheck['valid']) {
                throw new InvalidArgumentException($shiftCheck['message'] ?? 'الوقت المختار يقع خارج أوقات دوام الأخصائية.');
            }

            $itemEndTime24 = $this->availabilityService->calculateEndTime24($itemStart24, $duration);
            $timeline[] = [
                'service' => $item['service'],
                'specialist' => $sp,
                'duration_minutes' => $duration,
                'start_time' => $itemStart24,
                'end_time' => $itemEndTime24,
            ];

            $specialistCursors[$sp->id] = $itemEndTime24;
            $cursorTime = $itemEndTime24;
            $allStartTimes[] = $itemStart24;
            $allEndTimes[] = $itemEndTime24;
            $totalDurationMinutes += $duration;
        }

        $overallStartTime24 = ! empty($allStartTimes) ? min($allStartTimes) : $startTime24;
        $overallEndTime24 = ! empty($allEndTimes) ? max($allEndTimes) : $startTime24;
        $price = (float) array_sum(array_map(fn ($it) => (float) $it['service']->base_price, $resolvedItems));
        $vatAmount = round($price * 15 / 115, 2);
        $depositRequired = round(max($price * 0.30, min(50.0, $price)), 2);

        $booking = DB::transaction(function () use (
            $customer,
            $customerType,
            $timeline,
            $bookingDate,
            $overallStartTime24,
            $overallEndTime24,
            $price,
            $vatAmount,
            $depositRequired,
            $guestsCount,
            $customerNotes
        ): Booking {
            $specialistIds = array_values(array_unique(array_map(fn ($entry) => $entry['specialist']->id, $timeline)));
            sort($specialistIds);
            Specialist::query()->whereIn('id', $specialistIds)->lockForUpdate()->get();

            foreach ($timeline as $entry) {
                if ($this->availabilityService->hasOverlappingBooking(
                    specialistId: $entry['specialist']->id,
                    date: $bookingDate,
                    startTime24: $entry['start_time'],
                    endTime24: $entry['end_time']
                )) {
                    throw new SlotUnavailableException('عذراً، تم حجز هذا الموعد للتو من قبل عميلة أخرى. يرجى اختيار موعد آخر.');
                }
            }

            do {
                $bookingNumber = (string) random_int(1000, 9999);
            } while (Booking::where('booking_date', $bookingDate->toDateString())->where('booking_number', $bookingNumber)->exists());

            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'customer_id' => $customer->id,
                'customer_type' => $customerType,
                'customer_address_id' => null,
                'booking_type' => Booking::BOOKING_TYPE_SALON,
                'guests_count' => max(1, $guestsCount),
                'booking_date' => $bookingDate->toDateString(),
                'overall_start_time' => $overallStartTime24,
                'overall_end_time' => $overallEndTime24,
                'status' => Booking::STATUS_PENDING,
                'subtotal_amount' => $price,
                'discount_amount' => 0.00,
                'vat_amount' => $vatAmount,
                'total_amount' => $price,
                'deposit_required' => $depositRequired,
                'deposit_paid' => 0.00,
                'payment_status' => Booking::PAYMENT_STATUS_UNPAID,
                'admin_notes' => [
                    'ar' => $customerType === Booking::CUSTOMER_TYPE_GUEST ? 'حجز إلكتروني كـ زائر' : 'حجز إلكتروني مباشر من صفحة الحجز',
                    'en' => $customerType === Booking::CUSTOMER_TYPE_GUEST ? 'Online guest booking' : 'Direct online booking from booking page',
                ],
                'customer_notes' => $customerNotes ? trim($customerNotes) : null,
            ]);

            foreach ($timeline as $entry) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'service_id' => $entry['service']->id,
                    'specialist_id' => $entry['specialist']->id,
                    'hair_length_id' => null,
                    'unit_price' => (float) $entry['service']->base_price,
                    'duration_minutes' => $entry['duration_minutes'],
                    'scheduled_start_time' => $entry['start_time'],
                    'scheduled_end_time' => $entry['end_time'],
                    'status' => Booking::STATUS_PENDING,
                ]);
            }

            return $booking;
        });

        $primaryService = $resolvedItems[0]['service'];
        $serviceNames = array_map(fn ($it) => $it['service']->getTranslation('name', 'ar'), $resolvedItems);

        $specialistNames = [];
        foreach ($resolvedItems as $it) {
            $ar = $it['specialist']->getTranslation('name', 'ar');
            $en = $it['specialist']->getTranslation('name', 'en');
            $specialistNames[] = $en ? "{$ar} | {$en}" : $ar;
        }
        $specialistNames = array_values(array_unique($specialistNames));

        return [
            'id' => $booking->booking_number,
            'db_id' => $booking->id,
            'status' => $booking->status,
            'customerName' => $customer->name,
            'customerPhone' => $customer->phone,
            'customerEmail' => $customer->email,
            'categoryTitle' => $primaryService->category?->getTranslation('name', 'ar') ?? 'خدمات الصالون',
            'serviceName' => implode(' + ', $serviceNames),
            'specialistName' => implode(' + ', $specialistNames),
            'date' => $dateLabelClean,
            'bookingDateIso' => $bookingDate->toDateString(),
            'time' => $timeSlotClean,
            'price' => ((int) $price).' ر.س',
            'guests_count' => $booking->guests_count,
            'customer_notes' => $booking->customer_notes,
            'items' => array_map(fn ($entry) => [
                'serviceName' => $entry['service']->getTranslation('name', 'ar'),
                'specialistName' => $entry['specialist']->getTranslation('name', 'ar'),
                'startTime' => $this->availabilityService->format24HourToArabic($entry['start_time']),
                'endTime' => $this->availabilityService->format24HourToArabic($entry['end_time']),
                'durationMinutes' => $entry['duration_minutes'],
            ], $timeline),
        ];
    }
}
