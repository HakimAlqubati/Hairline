<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Booking\SpecialistAvailabilityService;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validates that the specialist does not have an existing active booking overlapping
 * the requested appointment interval (`[scheduled_start_time, scheduled_end_time)`) on the given date.
 */
class AvailableSpecialistSlot implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    public function __construct(
        private readonly ?string $specialistIdentifier = null,
        private readonly ?string $serviceIdentifier = null,
        private readonly ?string $bookingDateInput = null,
        private readonly ?int $ignoreBookingId = null,
        private readonly ?SpecialistAvailabilityService $availabilityService = null
    ) {}

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $availability = $this->availabilityService ?? app(SpecialistAvailabilityService::class);

        $timeString = trim((string) $value);
        if ($timeString === '') {
            return;
        }

        $startTime24 = $availability->parseTimeTo24Hour($timeString);
        if ($startTime24 === null) {
            return;
        }

        $dateInput = $this->bookingDateInput ?? (string) ($this->data['booking_date'] ?? '');
        $date = $availability->resolveBookingDate($dateInput);

        $today = Carbon::today('Asia/Riyadh');
        if ($date->isBefore($today)) {
            return;
        }

        $now = Carbon::now('Asia/Riyadh');
        if ($date->isSameDay($today)) {
            $slotMinutes = $availability->timeToMinutes($startTime24);
            $currentMinutes = ($now->hour * 60) + $now->minute;
            if ($slotMinutes < ($currentMinutes + 15)) {
                $fail('عذراً، هذا الموعد قد مضى وقته لليوم. يرجى اختيار موعد قادم.');

                return;
            }
        }

        if (! empty($this->data['items']) && is_array($this->data['items'])) {
            $resolvedItems = $availability->resolveBookingItems(null, null, $this->data['items']);
            $cursorTime = $startTime24;
            foreach ($resolvedItems as $item) {
                $duration = $item['duration_minutes'];
                $itemEndTime24 = $availability->calculateEndTime24($cursorTime, $duration);
                if ($availability->hasOverlappingBooking(
                    specialistId: $item['specialist']->id,
                    date: $date,
                    startTime24: $cursorTime,
                    endTime24: $itemEndTime24,
                    ignoreBookingId: $this->ignoreBookingId
                )) {
                    $fail('عذراً، هذا الموعد محجوز مسبقاً مع الأخصائية المختارة. يرجى اختيار وقت آخر متاح.');

                    return;
                }
                $cursorTime = $itemEndTime24;
            }

            return;
        }

        $specialistKey = $this->specialistIdentifier ?? (string) ($this->data['specialist_id'] ?? '');
        $specialist = $availability->resolveSpecialist($specialistKey);

        if (! $specialist) {
            return;
        }

        $serviceInput = $this->serviceIdentifier ?? ($this->data['service_ids'] ?? ($this->data['service_id'] ?? ''));
        $serviceCodes = is_array($serviceInput) ? $serviceInput : [$serviceInput];
        $totalDurationMinutes = 0;
        foreach ($serviceCodes as $sc) {
            $srv = $availability->resolveService((string) $sc);
            if ($srv) {
                $totalDurationMinutes += $availability->resolveDurationMinutes($specialist, $srv);
            }
        }
        if ($totalDurationMinutes === 0) {
            $totalDurationMinutes = 60;
        }

        $endTime24 = $availability->calculateEndTime24($startTime24, $totalDurationMinutes);

        $hasConflict = $availability->hasOverlappingBooking(
            specialistId: $specialist->id,
            date: $date,
            startTime24: $startTime24,
            endTime24: $endTime24,
            ignoreBookingId: $this->ignoreBookingId
        );

        if ($hasConflict) {
            $fail('عذراً، هذا الموعد محجوز مسبقاً مع الأخصائية المختارة. يرجى اختيار وقت آخر متاح.');
        }
    }
}
