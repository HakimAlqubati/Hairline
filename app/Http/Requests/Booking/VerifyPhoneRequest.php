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

class VerifyPhoneRequest extends BaseApiRequest
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
        $hasBooking = $this->filled('service_id') && $this->filled('specialist_id');

        $rules = [
            'phone' => ['required', 'string', new SaudiPhoneNumber($availabilityService)],
        ];

        if ($hasBooking) {
            $rules['service_id'] = ['required', 'string'];
            $rules['specialist_id'] = [
                'required',
                'string',
                new SpecialistOffersService($availabilityService),
            ];
            $rules['booking_date'] = [
                'required',
                'string',
                new NotPastDate($availabilityService),
            ];
            $rules['booking_time'] = [
                'required',
                'string',
                new WithinSpecialistShift(availabilityService: $availabilityService),
                new AvailableSpecialistSlot(availabilityService: $availabilityService),
            ];
            $rules['guests_count'] = ['nullable', 'integer', 'min:1', 'max:20'];
            $rules['customer_notes'] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
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
            'guests_count.integer' => 'عدد الأشخاص يجب أن يكون رقماً صحيحاً.',
            'guests_count.min' => 'عدد الأشخاص يجب أن يكون شخص واحد على الأقل.',
            'customer_notes.max' => 'ملاحظات العميلة يجب ألا تتجاوز 1000 حرف.',
        ];
    }
}
