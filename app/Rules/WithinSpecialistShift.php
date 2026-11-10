<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Booking\SpecialistAvailabilityService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validates that the requested booking date & time fall within the specialist's configured database shift
 * (`specialist_shifts` table: `day_of_week`, `start_time`, `end_time`, and `is_day_off = false`).
 */
class WithinSpecialistShift implements DataAwareRule, ValidationRule
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
            $fail('صيغة وقت الموعد المختار غير صالحة.');

            return;
        }

        $dateInput = $this->bookingDateInput ?? (string) ($this->data['booking_date'] ?? '');
        $date = $availability->resolveBookingDate($dateInput);

        if (! empty($this->data['items']) && is_array($this->data['items'])) {
            $hasExplicitItemTimes = false;
            foreach ($this->data['items'] as $it) {
                if (! empty($it['booking_time'])) {
                    $hasExplicitItemTimes = true;
                    break;
                }
            }

            $resolvedItems = $availability->resolveBookingItems(null, null, $this->data['items']);
            $specialistCursors = [];
            $cursorTime = $startTime24;

            foreach ($resolvedItems as $idx => $item) {
                $sp = $item['specialist'];
                $duration = $item['duration_minutes'];

                if ($hasExplicitItemTimes) {
                    $explicitTime = null;
                    if (! empty($this->data['items'][$idx]['booking_time'])) {
                        $explicitTime = $availability->parseTimeTo24Hour((string) $this->data['items'][$idx]['booking_time']);
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

                $shiftCheck = $availability->validateWithinShift(
                    specialist: $sp,
                    date: $date,
                    startTime24: $itemStart24,
                    durationMinutes: $duration
                );
                if (! $shiftCheck['valid']) {
                    $fail($shiftCheck['message'] ?? 'الوقت المختار يقع خارج أوقات دوام الأخصائية.');

                    return;
                }
                $specialistCursors[$sp->id] = $availability->calculateEndTime24($itemStart24, $duration);
                $cursorTime = $availability->calculateEndTime24($itemStart24, $duration);
            }

            return;
        }

        $specialistKey = $this->specialistIdentifier ?? (string) ($this->data['specialist_id'] ?? '');
        $specialist = $availability->resolveSpecialist($specialistKey);

        if (! $specialist) {
            $fail('الأخصائية المختارة غير متاحة حالياً.');

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

        $shiftCheck = $availability->validateWithinShift(
            specialist: $specialist,
            date: $date,
            startTime24: $startTime24,
            durationMinutes: $totalDurationMinutes
        );

        if (! $shiftCheck['valid']) {
            $fail($shiftCheck['message'] ?? 'الوقت المختار يقع خارج أوقات دوام الأخصائية.');
        }
    }
}
