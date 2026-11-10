<?php

declare(strict_types=1);

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseApiRequest;
use App\Rules\AvailableSpecialistSlot;
use App\Rules\NotPastDate;
use App\Rules\SaudiPhoneNumber;
use App\Rules\SpecialistOffersService;
use App\Rules\WithinSpecialistShift;
use App\Services\Booking\SpecialistAvailabilityService;

class ConfirmGuestBookingRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $availabilityService = app(SpecialistAvailabilityService::class);
        $rawPhone = $availabilityService->sanitizePhoneDigits((string) $this->input('phone', ''));
        $items = $this->input('items');
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (is_array($decoded)) {
                $this->merge(['items' => $decoded]);
            }
        }
        $this->merge(['phone' => $rawPhone]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $availabilityService = app(SpecialistAvailabilityService::class);

        return [
            'phone' => ['required', 'string', new SaudiPhoneNumber($availabilityService)],
            'name' => ['nullable', 'string', 'max:100'],
            'service_id' => ['required_without:items', 'nullable'],
            'specialist_id' => ['required_without:items', 'nullable', 'string', new SpecialistOffersService($availabilityService)],
            'items' => ['nullable', 'array'],
            'items.*.service_id' => ['required_with:items', 'string'],
            'items.*.specialist_id' => ['required_with:items', 'string', new SpecialistOffersService($availabilityService)],
            'items.*.booking_time' => ['nullable', 'string'],
            'booking_date' => ['required', 'string', new NotPastDate($availabilityService)],
            'booking_time' => [
                'required',
                'string',
                new WithinSpecialistShift(availabilityService: $availabilityService),
                new AvailableSpecialistSlot(availabilityService: $availabilityService),
            ],
            'guests_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'يرجى إدخال رقم الجوال للمتابعة.',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة أولاً.',
            'service_id.required_without' => 'يرجى اختيار الخدمة المطلوبة أولاً.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية أولاً.',
            'specialist_id.required_without' => 'يرجى اختيار الأخصائية أولاً.',
            'booking_date.required' => 'يرجى اختيار تاريخ الموعد أولاً.',
            'booking_time.required' => 'يرجى اختيار وقت الموعد أولاً.',
            'guests_count.integer' => 'عدد الأشخاص يجب أن يكون رقماً صحيحاً.',
            'guests_count.min' => 'عدد الأشخاص يجب أن يكون شخص واحد على الأقل.',
            'customer_notes.max' => 'ملاحظات العميلة يجب ألا تتجاوز 1000 حرف.',
        ];
    }
}
