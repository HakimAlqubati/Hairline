<?php

declare(strict_types=1);

namespace App\Contracts\Sms;

interface SmsGatewayInterface
{
    /**
     * Dispatch an SMS message to a normalized Saudi mobile phone number.
     *
     * @param  array<string, mixed>  $context
     */
    public function send(string $phone, string $message, array $context = []): bool;

    /**
     * Determine whether the gateway is operating in simulation mode.
     */
    public function isSimulation(): bool;
}
