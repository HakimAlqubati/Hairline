<?php

declare(strict_types=1);

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseApiRequest;

class GetAvailabilityRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $items = $this->input('items') ?: $this->query('items');
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (is_array($decoded)) {
                $this->merge(['items' => $decoded]);
            }
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'specialist_id' => ['required_without:items', 'nullable', 'string'],
            'service_id' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.service_id' => ['required_with:items', 'string'],
            'items.*.specialist_id' => ['required_with:items', 'string'],
            'booking_date' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'specialist_id.required_without' => 'يرجى تحديد الأخصائية لعرض الأوقات المتاحة.',
            'items.array' => 'قائمة الخدمات والأخصائيات غير صالحة.',
        ];
    }
}
