<?php

declare(strict_types=1);

namespace App\Services\Whatsapp;

use App\Contracts\Whatsapp\WhatsappServiceInterface;
use Illuminate\Support\Facades\Log;

class SimulatedWhatsappService implements WhatsappServiceInterface
{
    /**
     * Simulate dispatching a WhatsApp message during local development and testing.
     *
     * @param  array<string, mixed>  $context
     */
    public function send(string $phone, string $message, array $context = []): bool
    {
        Log::info('[Hairline Simulated WhatsApp Service]', [
            'phone' => $phone,
            'message' => $message,
            'driver' => 'simulated_whatsapp',
            'channel' => 'whatsapp',
            ...$context,
        ]);

        return true;
    }

    public function isSimulation(): bool
    {
        return true;
    }

    public function channelName(): string
    {
        return 'whatsapp';
    }
}
