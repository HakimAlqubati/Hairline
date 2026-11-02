<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Booking\SpecialistAvailabilityService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validates that the chosen specialist actually offers and is qualified to perform the selected service.
 */
class SpecialistOffersService implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    public function __construct(
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
        $serviceKey = (string) ($this->data['service_id'] ?? '');
        $specialistKey = trim((string) $value);

        if ($serviceKey === '' || $specialistKey === '') {
            return;
        }

        $service = $availability->resolveService($serviceKey);
        $specialist = $availability->resolveSpecialist($specialistKey);

        if (! $service || ! $specialist) {
            return;
        }

        if (! $availability->specialistOffersService($specialist, $service)) {
            $fail('عذراً، الأخصائية المختارة لا تقدم هذه الخدمة.');
        }
    }
}
