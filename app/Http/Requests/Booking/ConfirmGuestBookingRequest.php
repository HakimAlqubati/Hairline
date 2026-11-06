<?php

declare(strict_types=1);

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseApiRequest;
use App\Rules\SaudiPhoneNumber;
use App\Services\Booking\SpecialistAvailabilityService;

class ConfirmGuestBookingRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $availabilityService = app(SpecialistAvailabilityService::class);
        $rawPhone = $availabilityService->sanitizePhoneDigits((string) $this->input('phone', ''));
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
            'service_id' => ['required'],
            'specialist_id' => ['required', 'string'],
            'booking_date' => ['required', 'string'],
            'booking_time' => ['required', 'string'],
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
            'specialist_id.required' => 'يرجى اختيار الأخصائية أولاً.',
            'booking_date.required' => 'يرجى اختيار تاريخ الموعد أولاً.',
            'booking_time.required' => 'يرجى اختيار وقت الموعد أولاً.',
        ];
    }
}
