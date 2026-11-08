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

        $serviceKeys = [];
        if (preg_match('/^items\.(\d+)\.specialist_id$/', $attribute, $matches)) {
            $index = (int) $matches[1];
            $srvVal = $this->data['items'][$index]['service_id'] ?? '';
            if (is_array($srvVal)) {
                $serviceKeys = array_map('strval', $srvVal);
            } elseif ($srvVal !== '') {
                $serviceKeys = [(string) $srvVal];
            }
        } else {
            $srvVal = $this->data['service_id'] ?? null;
            if (is_array($srvVal)) {
                $serviceKeys = array_map('strval', $srvVal);
            } elseif ($srvVal !== null && $srvVal !== '') {
                $serviceKeys = [(string) $srvVal];
            }

            if (empty($serviceKeys) && ! empty($this->data['service_ids']) && is_array($this->data['service_ids'])) {
                $serviceKeys = array_map('strval', $this->data['service_ids']);
            }
        }

        $specialistKey = trim((string) $value);

        if ($specialistKey === '') {
            return;
        }

        $specialist = $availability->resolveSpecialist($specialistKey);
        if (! $specialist) {
            return;
        }

        foreach ($serviceKeys as $key) {
            $service = $availability->resolveService($key);
            if ($service && ! $availability->specialistOffersService($specialist, $service)) {
                $fail('عذراً، الأخصائية المختارة لا تقدم هذه الخدمة.');

                return;
            }
        }
    }
}
