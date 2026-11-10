<?php

declare(strict_types=1);

namespace App\Repositories\Otp;

use App\Contracts\Repositories\OtpRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CacheOtpRepository implements OtpRepositoryInterface
{
    private const OTP_CACHE_PREFIX = 'hairline:sms_otp:';

    private const COOLDOWN_CACHE_PREFIX = 'hairline:sms_otp_cooldown:';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(string $phone, string $codeHash, array $payload, int $ttlSeconds): void
    {
        $now = now()->getTimestamp();
        $expiresAt = $now + max(1, $ttlSeconds);

        Cache::put(
            key: $this->getOtpKey($phone),
            value: [
                'phone' => $phone,
                'code_hash' => $codeHash,
                'payload' => $payload,
                'attempts' => 0,
                'expires_at' => $expiresAt,
                'created_at' => $now,
            ],
            ttl: max(1, $ttlSeconds)
        );
    }

    /**
     * @return array{
     *     phone: string,
     *     code_hash: string,
     *     payload: array<string, mixed>,
     *     attempts: int,
     *     expires_at: int,
     *     created_at: int
     * }|null
     */
    public function get(string $phone): ?array
    {
        $record = Cache::get($this->getOtpKey($phone));

        if (! is_array($record)) {
            return null;
        }

        if (($record['expires_at'] ?? 0) < now()->getTimestamp()) {
            $this->delete($phone);

            return null;
        }

        return $record;
    }

    public function incrementAttempts(string $phone): int
    {
        $record = $this->get($phone);
        if (! $record) {
            return 0;
        }

        $record['attempts'] = ((int) ($record['attempts'] ?? 0)) + 1;
        $remainingTtl = max(1, ((int) $record['expires_at']) - now()->getTimestamp());

        Cache::put($this->getOtpKey($phone), $record, $remainingTtl);

        return $record['attempts'];
    }

    public function delete(string $phone): void
    {
        Cache::forget($this->getOtpKey($phone));
    }

    public function isOnCooldown(string $phone): bool
    {
        return $this->remainingCooldownSeconds($phone) > 0;
    }

    public function setCooldown(string $phone, int $cooldownSeconds): void
    {
        if ($cooldownSeconds <= 0) {
            Cache::forget($this->getCooldownKey($phone));

            return;
        }

        $expiresAt = now()->getTimestamp() + $cooldownSeconds;
        Cache::put($this->getCooldownKey($phone), $expiresAt, $cooldownSeconds);
    }

    public function remainingCooldownSeconds(string $phone): int
    {
        $expiresAt = Cache::get($this->getCooldownKey($phone));
        if (! is_numeric($expiresAt)) {
            return 0;
        }

        $remaining = ((int) $expiresAt) - now()->getTimestamp();

        if ($remaining <= 0) {
            Cache::forget($this->getCooldownKey($phone));

            return 0;
        }

        return $remaining;
    }

    private function getOtpKey(string $phone): string
    {
        return self::OTP_CACHE_PREFIX.$phone;
    }

    private function getCooldownKey(string $phone): string
    {
        return self::COOLDOWN_CACHE_PREFIX.$phone;
    }
}
