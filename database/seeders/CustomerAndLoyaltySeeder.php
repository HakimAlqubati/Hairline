<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\District;
use App\Models\LoyaltyPointTransaction;
use App\Models\MembershipTier;
use Illuminate\Database\Seeder;

class CustomerAndLoyaltySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membership Tiers
        $tiers = [
            'silver' => [
                'name_ar' => 'العضوية الفضية',
                'name_en' => 'Silver Tier',
                'color' => '#9CA3AF',
                'min_pts' => 0,
                'discount' => 5,
                'benefits' => 'خصم 5% على كافة الخدمات وجلسات العناية',
            ],
            'gold' => [
                'name_ar' => 'العضوية الذهبية',
                'name_en' => 'Gold Tier',
                'color' => '#C5A373',
                'min_pts' => 1000,
                'discount' => 10,
                'benefits' => 'خصم 10% وأولوية في تأكيد المواعيد وحجوزات الويكند',
            ],
            'vip' => [
                'name_ar' => 'عضوية كبار الشخصيات (VIP)',
                'name_en' => 'VIP Lounge Tier',
                'color' => '#211C18',
                'min_pts' => 2500,
                'discount' => 15,
                'benefits' => 'خصم 15% مع مشروبات ضيافة فاخرة واستشارة مجانية لصحة الشعر',
            ],
        ];

        $tierModels = [];
        $order = 1;
        foreach ($tiers as $slug => $t) {
            $tierModels[$slug] = MembershipTier::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => [
                        'ar' => $t['name_ar'],
                        'en' => $t['name_en'],
                    ],
                    'benefits_summary' => [
                        'ar' => $t['benefits'],
                        'en' => 'Exclusive member privileges and discounts.',
                    ],
                    'badge_color' => $t['color'],
                    'min_points_required' => $t['min_pts'],
                    'discount_percentage' => $t['discount'],
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );
        }

        // 2. Demo Customers
        $c1 = Customer::updateOrCreate(
            ['customer_code' => 'HL-1001'],
            [
                'membership_tier_id' => $tierModels['gold']->id,
                'name' => 'سارة العتيبي',
                'phone' => '0551234567',
                'email' => 'sara.otb@example.com',
                'total_points' => 2450,
                'is_active' => true,
            ]
        );

        $c2 = Customer::updateOrCreate(
            ['customer_code' => 'HL-2002'],
            [
                'membership_tier_id' => $tierModels['silver']->id,
                'name' => 'نورة الشمري',
                'phone' => '0559876543',
                'email' => 'noura.shm@example.com',
                'total_points' => 980,
                'is_active' => true,
            ]
        );

        // 3. Address for Customer 1
        $district = District::first();
        if ($district) {
            CustomerAddress::updateOrCreate(
                [
                    'customer_id' => $c1->id,
                    'district_id' => $district->id,
                ],
                [
                    'street_name' => 'طريق الأمير تركي بن عبدالعزيز الأول',
                    'building_number' => '14',
                    'floor_number' => '2',
                    'notes' => [
                        'ar' => 'فيلا خاصة - الاتصال عند الوصول',
                        'en' => 'Private villa - please call upon arrival',
                    ],
                    'latitude' => 24.7800,
                    'longitude' => 46.6600,
                    'is_default' => true,
                ]
            );
        }

        // 4. Sample Loyalty Transaction
        LoyaltyPointTransaction::updateOrCreate(
            [
                'customer_id' => $c1->id,
                'points_change' => 250,
            ],
            [
                'balance_after' => 2450,
                'description' => [
                    'ar' => 'مكافأة ولاء إتمام جلسة معالجة بروتين',
                    'en' => 'Loyalty reward for protein treatment session',
                ],
            ]
        );
    }
}
