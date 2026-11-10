<?php

declare(strict_types=1);

namespace App\Services\Sms;

use App\Contracts\Messaging\OtpChannelInterface;
use App\Contracts\Repositories\OtpRepositoryInterface;
use Illuminate\Support\Facades\Hash;

/**
 * Core OTP Verification Service responsible for:
 * 1. الإرسال (Sending / Simulating 4-digit OTP via WhatsApp or SMS, defaulting to "1100" in development)
 * 2. التشييك (Checking OTP status, cooldowns, expiration, attempt limits, and digit match)
 * 3. التأكيد (Verifying & consuming the OTP to authorize customer registration)
 */
class OtpVerificationService
{
    public const DEFAULT_DEV_CODE = '1100';

    public function __construct(
        private readonly OtpChannelInterface $otpChannel,
        private readonly OtpRepositoryInterface $otpRepository
    ) {}

    /**
     * Return the active channel identifier ('whatsapp' or 'sms').
     */
    public function getChannelName(): string
    {
        return $this->otpChannel->channelName();
    }

    /**
     * Return the active OTP channel instance.
     */
    public function getChannel(): OtpChannelInterface
    {
        return $this->otpChannel;
    }

    /**
     * Determine whether simulation mode is active.
     */
    public function isSimulationMode(): bool
    {
        return (bool) config('sms.otp.simulation_mode', true) || $this->otpChannel->isSimulation();
    }

    /**
     * Generate a 4-digit OTP code.
     * Returns "1100" when simulation mode is enabled; otherwise generates a cryptographic 4-digit code.
     */
    public function generateCode(): string
    {
        $length = max(4, (int) config('sms.otp.length', 4));

        if ($this->isSimulationMode()) {
            $defaultCode = trim((string) config('sms.otp.default_code', self::DEFAULT_DEV_CODE));

            return $defaultCode !== '' ? $defaultCode : self::DEFAULT_DEV_CODE;
        }

        $max = (10 ** $length) - 1;

        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Normalize Arabic/Persian numerals in the OTP code to standard Western digits.
     */
    public function sanitizeCode(string $rawCode): string
    {
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $normalized = str_replace($eastern, $western, trim($rawCode));

        return preg_replace('/\D+/', '', $normalized) ?? '';
    }

    /**
     * 1. الإرسال (Send & Simulate OTP):
     * Generate, persist, and dispatch the 4-digit verification code to the customer's phone via active channel (WhatsApp/SMS).
     *
     * @param  array<string, mixed>  $meta
     * @return array{
     *     sent: bool,
     *     status: string,
     *     channel: string,
     *     channel_label: string,
     *     message: string,
     *     phone: string,
     *     masked_phone: string,
     *     expires_in: int,
     *     resend_after: int,
     *     is_simulation: bool,
     *     simulated_code: ?string
     * }
     */
    public function sendOtp(string $normalizedPhone, string $customerName, array $meta = [], bool $enforceCooldown = false): array
    {
        $channel = $this->otpChannel->channelName();
        $channelLabelAr = $channel === 'whatsapp' ? 'واتساب' : 'رسالة نصية SMS';

        if ($enforceCooldown && $this->otpRepository->isOnCooldown($normalizedPhone)) {
            $remaining = $this->otpRepository->remainingCooldownSeconds($normalizedPhone);

            return [
                'sent' => false,
                'status' => 'cooldown_active',
                'channel' => $channel,
                'channel_label' => $channelLabelAr,
                'message' => "يرجى الانتظار ({$remaining}) ثانية قبل طلب رمز تحقق جديد.",
                'phone' => $normalizedPhone,
                'masked_phone' => $this->maskPhone($normalizedPhone),
                'expires_in' => 0,
                'resend_after' => $remaining,
                'is_simulation' => $this->isSimulationMode(),
                'simulated_code' => null,
            ];
        }

        $code = $this->generateCode();
        $ttlSeconds = max(60, (int) config('sms.otp.ttl_seconds', 300));
        $cooldownSeconds = max(0, (int) config('sms.otp.resend_cooldown_seconds', 30));

        $this->otpRepository->store(
            phone: $normalizedPhone,
            codeHash: Hash::make($code),
            payload: [
                'name' => trim($customerName),
                'channel' => $channel,
                ...$meta,
            ],
            ttlSeconds: $ttlSeconds
        );

        if ($cooldownSeconds > 0) {
            $this->otpRepository->setCooldown($normalizedPhone, $cooldownSeconds);
        }

        $dispatchMessage = $channel === 'whatsapp'
            ? "مرحباً {$customerName} 👋\nرمز التحقق لتأكيد تسجيلكِ وحجزكِ في صالون هيرلاين هو: *{$code}*\nالرمز صالح لمدة 5 دقائق."
            : "مرحباً {$customerName}، رمز التحقق لتأكيد تسجيلكِ وحجزكِ في صالون هيرلاين هو: {$code}";

        $this->otpChannel->send($normalizedPhone, $dispatchMessage, [
            'customer_name' => $customerName,
            'otp_code' => $this->isSimulationMode() ? $code : '****',
            'channel' => $channel,
        ]);

        return [
            'sent' => true,
            'status' => 'otp_sent',
            'channel' => $channel,
            'channel_label' => $channelLabelAr,
            'message' => $channel === 'whatsapp'
                ? 'تم إرسال رمز التحقق المكون من 4 أرقام عبر واتساب إلى رقم جوالكِ بنجاح.'
                : 'تم إرسال رمز التحقق المكون من 4 أرقام إلى رقم جوالكِ بنجاح.',
            'phone' => $normalizedPhone,
            'masked_phone' => $this->maskPhone($normalizedPhone),
            'expires_in' => $ttlSeconds,
            'resend_after' => $cooldownSeconds,
            'is_simulation' => $this->isSimulationMode(),
            'simulated_code' => $this->isSimulationMode() ? $code : null,
        ];
    }

    /**
     * 2. التشييك (Check OTP State & Validity without consuming):
     * Inspect whether an OTP exists, has not expired, has not exceeded max attempts, and matches the 4-digit code.
     *
     * @return array{
     *     valid: bool,
     *     status: string,
     *     message: string,
     *     record: ?array<string, mixed>
     * }
     */
    public function checkOtp(string $normalizedPhone, string $inputCode): array
    {
        $cleanCode = $this->sanitizeCode($inputCode);
        $expectedLength = max(4, (int) config('sms.otp.length', 4));

        if (mb_strlen($cleanCode) !== $expectedLength) {
            return [
                'valid' => false,
                'status' => 'invalid_format',
                'message' => "يرجى إدخال رمز التحقق المكون من {$expectedLength} أرقام بشكل كامل.",
                'record' => null,
            ];
        }

        $record = $this->otpRepository->get($normalizedPhone);
        if (! $record) {
            return [
                'valid' => false,
                'status' => 'otp_expired',
                'message' => 'انتهت صلاحية رمز التحقق أو لم يتم طلبه بعد. يرجى إعادة إرسال الرمز.',
                'record' => null,
            ];
        }

        $maxAttempts = max(1, (int) config('sms.otp.max_attempts', 5));
        $currentAttempts = (int) ($record['attempts'] ?? 0);

        if ($currentAttempts >= $maxAttempts) {
            $this->otpRepository->delete($normalizedPhone);

            return [
                'valid' => false,
                'status' => 'max_attempts_exceeded',
                'message' => 'تم تجاوز الحد الأقصى لمحاولات التحقق. يرجى طلب رمز جديد.',
                'record' => null,
            ];
        }

        $isMatch = Hash::check($cleanCode, (string) ($record['code_hash'] ?? ''));

        if (! $isMatch) {
            return [
                'valid' => false,
                'status' => 'invalid_code',
                'message' => 'رمز التحقق المدخل غير صحيح، يرجى التأكد من الأربعة أرقام والمحاولة مرة أخرى.',
                'record' => $record,
            ];
        }

        return [
            'valid' => true,
            'status' => 'valid',
            'message' => 'رمز التحقق صحيح.',
            'record' => $record,
        ];
    }

    /**
     * 3. التأكيد (Verify & Consume OTP):
     * Perform full verification check; increment attempts on failure or consume the OTP on success.
     *
     * @return array{
     *     verified: bool,
     *     status: string,
     *     message: string,
     *     payload: array<string, mixed>,
     *     remaining_attempts: int
     * }
     */
    public function verifyOtp(string $normalizedPhone, string $inputCode): array
    {
        $maxAttempts = max(1, (int) config('sms.otp.max_attempts', 5));
        $checkResult = $this->checkOtp($normalizedPhone, $inputCode);

        if (! $checkResult['valid']) {
            $remaining = $maxAttempts;

            if ($checkResult['status'] === 'invalid_code') {
                $attempts = $this->otpRepository->incrementAttempts($normalizedPhone);
                $remaining = max(0, $maxAttempts - $attempts);

                if ($remaining === 0) {
                    $this->otpRepository->delete($normalizedPhone);

                    return [
                        'verified' => false,
                        'status' => 'max_attempts_exceeded',
                        'message' => 'تم تجاوز الحد الأقصى لمحاولات التحقق. يرجى طلب رمز جديد.',
                        'payload' => [],
                        'remaining_attempts' => 0,
                    ];
                }
            }

            return [
                'verified' => false,
                'status' => $checkResult['status'],
                'message' => $checkResult['message'],
                'payload' => [],
                'remaining_attempts' => $remaining,
            ];
        }

        $payload = (array) ($checkResult['record']['payload'] ?? []);
        $this->otpRepository->delete($normalizedPhone);

        return [
            'verified' => true,
            'status' => 'verified',
            'message' => 'تم تأكيد رقم الجوال بنجاح.',
            'payload' => $payload,
            'remaining_attempts' => $maxAttempts,
        ];
    }

    /**
     * Mask a Saudi mobile number for display (e.g., 0551234567 -> 055***4567).
     */
    public function maskPhone(string $phone): string
    {
        if (mb_strlen($phone) < 7) {
            return $phone;
        }

        return mb_substr($phone, 0, 3).'***'.mb_substr($phone, -4);
    }
}
