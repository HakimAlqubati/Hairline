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
     *
     * @throws InvalidArgumentException
     * @throws SlotUnavailableException
     */
    public function createBookingForCustomer(
        Customer $customer,
        string $serviceCode,
        string $specialistSlug,
        string $dateLabel,
        string $timeSlot
    ): array {
        $service = $this->availabilityService->resolveService($serviceCode);
        if (! $service) {
            throw new InvalidArgumentException('الخدمة المختارة غير متاحة.');
        }

        $specialist = $this->availabilityService->resolveSpecialist($specialistSlug);
        if (! $specialist) {
            throw new InvalidArgumentException('الأخصائية المختارة غير متاحة.');
        }

        if (! $this->availabilityService->specialistOffersService($specialist, $service)) {
            throw new InvalidArgumentException('عذراً، الأخصائية المختارة لا تقدم هذه الخدمة.');
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
        $durationMinutes = $this->availabilityService->resolveDurationMinutes($specialist, $service);
        $shiftCheck = $this->availabilityService->validateWithinShift($specialist, $bookingDate, $startTime24, $durationMinutes);
        if (! $shiftCheck['valid']) {
            throw new InvalidArgumentException($shiftCheck['message'] ?? 'الوقت المختار يقع خارج أوقات دوام الأخصائية.');
        }

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
            // Lock specialist row for update to serialize concurrent booking attempts for this specialist
            $lockedSpecialist = Specialist::whereKey($specialist->id)->lockForUpdate()->first();
            if (! $lockedSpecialist) {
                throw new InvalidArgumentException('الأخصائية المختارة غير موجودة.');
            }

            // Re-check for overlapping bookings inside the database lock to eliminate race conditions
            if ($this->availabilityService->hasOverlappingBooking(
                specialistId: $specialist->id,
                date: $bookingDate,
                startTime24: $startTime24,
                endTime24: $endTime24
            )) {
                throw new SlotUnavailableException('عذراً، تم حجز هذا الموعد للتو من قبل عميلة أخرى. يرجى اختيار موعد آخر.');
            }

            do {
                $bookingNumber = (string) random_int(1000, 9999);
            } while (Booking::where('booking_date', $bookingDate->toDateString())->where('booking_number', $bookingNumber)->exists());

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
            'date' => $dateLabelClean,
            'bookingDateIso' => $bookingDate->toDateString(),
            'time' => $timeSlotClean,
            'price' => ((int) $price).' ر.س',
        ];
    }
}
