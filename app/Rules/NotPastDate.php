<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Booking\SpecialistAvailabilityService;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validates that the requested booking date is not in the past relative to Riyadh time.
 */
class NotPastDate implements ValidationRule
{
    public function __construct(
        private readonly ?SpecialistAvailabilityService $availabilityService = null
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dateInput = trim((string) $value);
        if ($dateInput === '') {
            return;
        }

        $availability = $this->availabilityService ?? app(SpecialistAvailabilityService::class);
        $date = $availability->resolveBookingDate($dateInput);

        $today = Carbon::today('Asia/Riyadh');
        if ($date->isBefore($today)) {
            $fail('لا يمكن حجز موعد في تاريخ سابق.');
        }
    }
}
