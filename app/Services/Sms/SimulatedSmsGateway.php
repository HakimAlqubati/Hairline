<?php

declare(strict_types=1);

namespace App\Services\Sms;

use App\Contracts\Sms\SmsGatewayInterface;
use Illuminate\Support\Facades\Log;

class SimulatedSmsGateway implements SmsGatewayInterface
{
    /**
     * Simulate dispatching an SMS message during local development and testing.
     *
     * @param  array<string, mixed>  $context
     */
    public function send(string $phone, string $message, array $context = []): bool
    {
        Log::info('[Hairline Simulated SMS Gateway]', [
            'phone' => $phone,
            'message' => $message,
            'driver' => 'simulated',
            ...$context,
        ]);

        return true;
    }

    public function isSimulation(): bool
    {
        return true;
    }
}
