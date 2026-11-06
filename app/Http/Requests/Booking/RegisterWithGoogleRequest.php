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

class RegisterWithGoogleRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $availabilityService = app(SpecialistAvailabilityService::class);
        $rawPhone = $availabilityService->sanitizePhoneDigits((string) $this->input('phone', ''));
        $this->merge([
            'phone' => $rawPhone !== '' ? $rawPhone : null,
            'code' => trim((string) $this->input('code', '')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $availabilityService = app(SpecialistAvailabilityService::class);
        $isProfile = $this->boolean('is_profile');
        $hasBooking = ! $isProfile && $this->filled('service_id') && $this->filled('specialist_id');

        $rules = [
            'code' => ['required', 'string'],
            'phone' => ['nullable', 'string', new SaudiPhoneNumber($availabilityService)],
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
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'رمز التحقق من حساب Google مطلوب لإتمام العملية.',
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة أولاً.',
            'specialist_id.required' => 'يرجى اختيار الأخصائية أولاً.',
            'booking_date.required' => 'يرجى اختيار تاريخ الموعد أولاً.',
            'booking_time.required' => 'يرجى اختيار وقت الموعد أولاً.',
        ];
    }
}
