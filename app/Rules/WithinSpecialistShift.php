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

        $specialistKey = $this->specialistIdentifier ?? (string) ($this->data['specialist_id'] ?? '');
        $specialist = $availability->resolveSpecialist($specialistKey);

        if (! $specialist) {
            $fail('الأخصائية المختارة غير متاحة حالياً.');

            return;
        }

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

        $serviceKey = $this->serviceIdentifier ?? (string) ($this->data['service_id'] ?? '');
        $service = $availability->resolveService($serviceKey);
        $durationMinutes = $availability->resolveDurationMinutes($specialist, $service);

        $shiftCheck = $availability->validateWithinShift(
            specialist: $specialist,
            date: $date,
            startTime24: $startTime24,
            durationMinutes: $durationMinutes
        );

        if (! $shiftCheck['valid']) {
            $fail($shiftCheck['message'] ?? 'الوقت المختار يقع خارج أوقات دوام الأخصائية.');
        }
    }
}
