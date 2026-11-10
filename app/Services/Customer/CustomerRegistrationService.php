<?php

declare(strict_types=1);

namespace App\Services\Customer;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Models\Customer;
use App\Services\Sms\OtpVerificationService;
use InvalidArgumentException;

class CustomerRegistrationService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly OtpVerificationService $otpVerificationService
    ) {}

    /**
     * Initiate customer registration by dispatching a 4-digit SMS OTP code.
     *
     * @param  array<string, mixed>  $bookingMeta
     * @return array{
     *     sent: bool,
     *     status: string,
     *     message: string,
     *     phone: string,
     *     masked_phone: string,
     *     expires_in: int,
     *     resend_after: int,
     *     is_simulation: bool,
     *     simulated_code: ?string
     * }
     */
    public function initiateRegistrationOtp(string $normalizedPhone, string $customerName, array $bookingMeta = []): array
    {
        $cleanName = trim($customerName);
        if (mb_strlen($cleanName) < 2) {
            throw new InvalidArgumentException('يرجى إدخال اسمكِ الكريم (حرفين على الأقل) للمتابعة.');
        }

        return $this->otpVerificationService->sendOtp(
            normalizedPhone: $normalizedPhone,
            customerName: $cleanName,
            meta: $bookingMeta
        );
    }

    /**
     * Verify the 4-digit SMS OTP and register/update the customer in the database.
     *
     * @return array{
     *     verified: bool,
     *     status: string,
     *     message: string,
     *     customer: ?Customer,
     *     remaining_attempts: int
     * }
     */
    public function verifyOtpAndRegisterCustomer(string $normalizedPhone, string $otpCode, ?string $fallbackName = null): array
    {
        $verification = $this->otpVerificationService->verifyOtp($normalizedPhone, $otpCode);

        if (! $verification['verified']) {
            return [
                'verified' => false,
                'status' => $verification['status'],
                'message' => $verification['message'],
                'customer' => null,
                'remaining_attempts' => $verification['remaining_attempts'],
            ];
        }

        $storedName = trim((string) ($verification['payload']['name'] ?? ''));
        $finalName = $storedName !== '' ? $storedName : trim((string) $fallbackName);

        if (mb_strlen($finalName) < 2) {
            return [
                'verified' => false,
                'status' => 'validation_error',
                'message' => 'يرجى إدخال الاسم الكريم لإتمام التسجيل.',
                'customer' => null,
                'remaining_attempts' => 0,
            ];
        }

        $customer = $this->customerRepository->registerOrUpdateByPhone(
            normalizedPhone: $normalizedPhone,
            name: $finalName
        );

        return [
            'verified' => true,
            'status' => 'verified',
            'message' => "تم تأكيد الرمز وتسجيل حسابكِ بنجاح يا {$customer->name}.",
            'customer' => $customer,
            'remaining_attempts' => $verification['remaining_attempts'],
        ];
    }
}
