<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

interface OtpRepositoryInterface
{
    /**
     * Store a hashed OTP verification record and associated registration payload.
     *
     * @param  array<string, mixed>  $payload
     */
    public function store(string $phone, string $codeHash, array $payload, int $ttlSeconds): void;

    /**
     * Retrieve the active OTP verification record for the given phone number.
     *
     * @return array{
     *     phone: string,
     *     code_hash: string,
     *     payload: array<string, mixed>,
     *     attempts: int,
     *     expires_at: int,
     *     created_at: int
     * }|null
     */
    public function get(string $phone): ?array;

    /**
     * Increment the failed verification attempt counter and return the updated count.
     */
    public function incrementAttempts(string $phone): int;

    /**
     * Delete/consume the OTP record for the given phone number.
     */
    public function delete(string $phone): void;

    /**
     * Check if the phone number is currently in a resend cooldown window.
     */
    public function isOnCooldown(string $phone): bool;

    /**
     * Put the phone number on resend cooldown for the given number of seconds.
     */
    public function setCooldown(string $phone, int $cooldownSeconds): void;

    /**
     * Get the remaining resend cooldown in seconds (0 if not on cooldown).
     */
    public function remainingCooldownSeconds(string $phone): int;
}
