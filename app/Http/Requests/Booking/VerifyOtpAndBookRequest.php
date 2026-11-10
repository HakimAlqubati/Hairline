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
use App\Services\Sms\OtpVerificationService;

class VerifyOtpAndBookRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $availabilityService = app(SpecialistAvailabilityService::class);
        $otpService = app(OtpVerificationService::class);

        $rawPhone = $availabilityService->sanitizePhoneDigits((string) $this->input('phone', ''));
        $rawName = trim((string) $this->input('name', ''));
        $rawOtp = $otpService->sanitizeCode((string) $this->input('otp_code', ''));

        $items = $this->input('items');
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (is_array($decoded)) {
                $this->merge(['items' => $decoded]);
            }
        }

        $this->merge([
            'phone' => $rawPhone,
            'name' => $rawName,
            'otp_code' => $rawOtp,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $availabilityService = app(SpecialistAvailabilityService::class);
        $hasBooking = ($this->filled('service_id') && $this->filled('specialist_id')) || $this->filled('items');

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string', new SaudiPhoneNumber($availabilityService)],
            'otp_code' => ['required', 'string', 'digits:4'],
        ];

        if ($hasBooking) {
            $rules['service_id'] = ['required_without:items', 'nullable'];
            $rules['specialist_id'] = [
                'required_without:items',
                'nullable',
                'string',
                new SpecialistOffersService($availabilityService),
            ];
            $rules['items'] = ['nullable', 'array'];
            $rules['items.*.service_id'] = ['required_with:items', 'string'];
            $rules['items.*.specialist_id'] = ['required_with:items', 'string', new SpecialistOffersService($availabilityService)];
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
            'name.required' => 'يرجى إدخال اسمكِ الكريم لإتمام التسجيل.',
            'name.min' => 'الاسم الكريم يجب أن يتكون من حرفين على الأقل.',
            'name.max' => 'الاسم الكريم يجب ألا يتجاوز 100 حرف.',
            'phone.required' => 'يرجى إدخال رقم الجوال للمتابعة.',
            'otp_code.required' => 'يرجى إدخال رمز التحقق المكون من 4 أرقام.',
            'otp_code.digits' => 'رمز التحقق يجب أن يتكون من 4 أرقام بالضبط.',
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
