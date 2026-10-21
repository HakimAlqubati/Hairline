<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CancellationPolicy;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PolicyAndPaymentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Payment Methods
        $paymentOptions = [
            [
                'id' => 'mada',
                'name_ar' => 'بطاقة مدى',
                'name_en' => 'Mada Card',
                'icon' => 'credit-card',
                'is_available' => true,
                'fee' => 0,
                'description_ar' => 'الدفع الفوري الآمن عبر بطاقات مدى السعودية',
            ],
            [
                'id' => 'apple_pay',
                'name_ar' => 'Apple Pay',
                'name_en' => 'Apple Pay',
                'icon' => 'apple',
                'is_available' => true,
                'fee' => 0,
                'description_ar' => 'دفع سريع وآمن بلمسة واحدة عبر أجهزتكِ',
            ],
            [
                'id' => 'visa_mc',
                'name_ar' => 'فيزا / ماستركارد',
                'name_en' => 'Visa / Mastercard',
                'icon' => 'credit-card',
                'is_available' => true,
                'fee' => 0,
                'description_ar' => 'البطاقات الائتمانية والخصم المباشر العالمية',
            ],
            [
                'id' => 'cash',
                'name_ar' => 'الدفع في الصالون (نقداً)',
                'name_en' => 'Cash at Salon',
                'icon' => 'money-bill-wave',
                'is_available' => true,
                'fee' => 0,
                'description_ar' => 'يُدفع العربون إلكترونياً والمتبقي عند الوصول',
            ],
        ];

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
                        'ar' => $opt['description_ar'],
                        'en' => 'Secure online or in-salon payment option.',
                    ],
                    'icon' => $opt['icon'],
                    'fee' => (float) $opt['fee'],
                    'sort_order' => $order++,
                    'is_available' => (bool) $opt['is_available'],
                ]
            );
        }

        // 2. Cancellation Policy
        CancellationPolicy::updateOrCreate(
            ['id' => 1],
            [
                'policy_text' => [
                    'ar' => 'يمكن إلغاء الحجز مجاناً قبل 12 ساعة من الموعد. الإلغاء بين 6-12 ساعة يستوجب خصم 50% من العربون. الإلغاء بعد أقل من 6 ساعات أو عدم الحضور يُفقدكِ العربون كاملاً.',
                    'en' => 'Free cancellation is available up to 12 hours before appointment.',
                ],
                'free_cancellation_hours' => 12,
                'partial_refund_hours' => 6,
                'partial_refund_percentage' => 50,
                'no_show_penalty_percentage' => 100,
                'is_active' => true,
            ]
        );
    }
}
