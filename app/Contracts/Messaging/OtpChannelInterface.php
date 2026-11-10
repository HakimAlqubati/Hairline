<?php

declare(strict_types=1);

namespace App\Contracts\Messaging;

interface OtpChannelInterface
{
    /**
     * Dispatch an OTP verification message to a normalized phone number.
     *
     * @param  array<string, mixed>  $context
     */
    public function send(string $phone, string $message, array $context = []): bool;

    /**
     * Determine whether the channel is operating in simulation mode.
     */
    public function isSimulation(): bool;

    /**
     * Return the unique identifier for this channel (e.g. 'whatsapp', 'sms').
     */
    public function channelName(): string;
}
