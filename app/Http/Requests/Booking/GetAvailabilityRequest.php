<?php

declare(strict_types=1);

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseApiRequest;

class GetAvailabilityRequest extends BaseApiRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'specialist_id' => ['required', 'string'],
            'service_id' => ['nullable', 'string'],
            'booking_date' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'specialist_id.required' => 'يرجى تحديد الأخصائية لعرض الأوقات المتاحة.',
        ];
    }
}
