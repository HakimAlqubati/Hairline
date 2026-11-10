<?php

declare(strict_types=1);

namespace App\Contracts\Whatsapp;

use App\Contracts\Messaging\OtpChannelInterface;

interface WhatsappServiceInterface extends OtpChannelInterface
{
    /**
     * Dispatch a WhatsApp message to a normalized phone number.
     *
     * @param  array<string, mixed>  $context
     */
    public function send(string $phone, string $message, array $context = []): bool;

    /**
     * Determine whether the service is operating in simulation mode.
     */
    public function isSimulation(): bool;

    /**
     * Return the channel identifier ('whatsapp').
     */
    public function channelName(): string;
}
