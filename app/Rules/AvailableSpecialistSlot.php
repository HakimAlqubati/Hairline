<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Booking\SpecialistAvailabilityService;
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

        $specialistKey = $this->specialistIdentifier ?? (string) ($this->data['specialist_id'] ?? '');
        $specialist = $availability->resolveSpecialist($specialistKey);

        if (! $specialist) {
            return;
        }

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

        $serviceKey = $this->serviceIdentifier ?? (string) ($this->data['service_id'] ?? '');
        $service = $availability->resolveService($serviceKey);
        $durationMinutes = $availability->resolveDurationMinutes($specialist, $service);
        $endTime24 = $availability->calculateEndTime24($startTime24, $durationMinutes);

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
