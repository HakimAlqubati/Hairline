<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Booking\SpecialistAvailabilityService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validates that an input is a valid Saudi mobile number (05XXXXXXXX, +9665XXXXXXXX, 009665XXXXXXXX, 5XXXXXXXX).
 */
class SaudiPhoneNumber implements ValidationRule
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
        if (! is_string($value) && ! is_numeric($value)) {
            $fail('يرجى إدخال رقم جوال سعودي صحيح يبدأ بـ 05 ويتكون من 10 أرقام (مثال: 0551234567).');

            return;
        }

        $service = $this->availabilityService ?? app(SpecialistAvailabilityService::class);

        if (! $service->isValidSaudiPhone((string) $value)) {
            $fail('يرجى إدخال رقم جوال سعودي صحيح يبدأ بـ 05 ويتكون من 10 أرقام (مثال: 0551234567).');
        }
    }
}
