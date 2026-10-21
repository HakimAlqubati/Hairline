<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CancellationPolicy;
use App\Models\PaymentMethod;
use App\Services\FrontData\CartAndCheckoutDataService;
use Illuminate\Database\Seeder;

class PolicyAndPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $checkoutData = app(CartAndCheckoutDataService::class);

        // 1. Payment Methods
        $paymentOptions = $checkoutData->getPaymentOptions();
        $order = 1;

        foreach ($paymentOptions as $opt) {
            PaymentMethod::updateOrCreate(
                ['code' => $opt['id']],
                [
                    'name' => [
                        'ar' => $opt['name_ar'],
                        'en' => $opt['name_en'],
                    ],
                    'description' => [
                        'ar' => $opt['description_ar'] ?? '',
                        'en' => 'Secure online or in-salon payment option.',
                    ],
                    'icon' => $opt['icon'] ?? 'credit-card',
                    'fee' => (float) ($opt['fee'] ?? 0),
                    'sort_order' => $order++,
                    'is_available' => (bool) ($opt['is_available'] ?? true),
                ]
            );
        }

        // 2. Cancellation Policy
        $policy = $checkoutData->getCancellationPolicy();
        CancellationPolicy::updateOrCreate(
            ['id' => 1],
            [
                'policy_text' => [
                    'ar' => $policy['policy_ar'] ?? 'يمكن إلغاء الحجز مجاناً قبل 12 ساعة من الموعد.',
                    'en' => 'Free cancellation is available up to 12 hours before appointment.',
                ],
                'free_cancellation_hours' => $policy['free_cancellation_hours'] ?? 12,
                'partial_refund_hours' => $policy['partial_refund_hours'] ?? 6,
                'partial_refund_percentage' => $policy['partial_refund_percentage'] ?? 50,
                'no_show_penalty_percentage' => $policy['no_show_fee_percentage'] ?? 100,
                'is_active' => true,
            ]
        );
    }
}
