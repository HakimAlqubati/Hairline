<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Gateway Driver
    |--------------------------------------------------------------------------
    |
    | Controls the SMS gateway implementation bound to SmsGatewayInterface.
    | Supported drivers: "simulated" (development/testing), "production".
    |
    */
    'driver' => env('SMS_DRIVER', 'simulated'),

    /*
    |--------------------------------------------------------------------------
    | SMS OTP Verification & Simulation Configuration
    |--------------------------------------------------------------------------
    |
    | During local development and testing, simulation_mode is enabled by
    | default and uses the fixed 4-digit code "1100" so verification flows
    | can be tested end-to-end without external SMS charges.
    |
    */
    'otp' => [
        'length' => (int) env('SMS_OTP_LENGTH', 4),
        'simulation_mode' => (bool) env('SMS_SIMULATION_MODE', true),
        'default_code' => (string) env('SMS_DEFAULT_CODE', '1100'),
        'ttl_seconds' => (int) env('SMS_OTP_TTL', 300),
        'max_attempts' => (int) env('SMS_OTP_MAX_ATTEMPTS', 5),
        'resend_cooldown_seconds' => (int) env('SMS_OTP_RESEND_COOLDOWN', 30),
    ],

];
