<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseApiRequest;
use App\Rules\SaudiPhoneNumber;
use App\Services\Booking\SpecialistAvailabilityService;
use Illuminate\Validation\Rule;

class UpdateCustomerProfileRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $availabilityService = app(SpecialistAvailabilityService::class);
        $rawPhone = $availabilityService->sanitizePhoneDigits((string) $this->input('phone', ''));
        $normalizedPhone = $rawPhone !== '' ? $availabilityService->normalizeSaudiPhone($rawPhone) : null;
        $this->merge(['phone' => $normalizedPhone]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $customerId = session('logged_in_customer_id');
        $availabilityService = app(SpecialistAvailabilityService::class);

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => [
                'nullable',
                'string',
                new SaudiPhoneNumber($availabilityService),
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال الاسم الكامل.',
            'name.min' => 'يجب أن يتكون الاسم من حرفين على الأقل.',
            'phone.unique' => 'رقم الجوال هذا مسجل بالفعل لحساب آخر.',
            'avatar.image' => 'يرجى اختيار ملف صورة صالح.',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 4 ميجابايت.',
        ];
    }
}
