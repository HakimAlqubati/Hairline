<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Specialist;
use App\Models\SpecialistShift;
use Illuminate\Database\Seeder;

class SpecialistSeeder extends Seeder
{
    public function run(): void
    {
        $specialists = [
            'sp_karen' => [
                'name_ar' => 'كارين',
                'name_en' => 'Karen',
                'role' => 'خبيرة قص ومعالجات وصبغات الشعر والتسريحات',
                'experience' => 'خبرة لأكثر من 15 عامًا',
                'avatar' => '/images/specialists/karen.png',
                'bio' => 'خبرة واسعة في مجال قص الشعر بمختلف أنواعه، ومعالجات الشعر، والصبغات، والتسريحات، بخبرة واسعة في تقديم إطلالات تناسب مختلف الأذواق.',
                'is_home' => false,
            ],
            'sp_janet' => [
                'name_ar' => 'جانيت',
                'name_en' => 'Jannet',
                'role' => 'أخصائية خدمات الشعر والصبغات والمعالجات',
                'experience' => 'خبرة لأكثر من 13 عامًا',
                'avatar' => '/images/specialists/jannet.png',
                'bio' => 'تمتع بخبرة شاملة في خدمات الشعر، وتتميز باحترافيتها في الصبغات ومعالجات الشعر، مع اهتمام بأدق التفاصيل للوصول إلى نتائج مميزة.',
                'is_home' => false,
            ],
            'sp_jenky' => [
                'name_ar' => 'جينكي',
                'name_en' => 'Jenky',
                'role' => 'أخصائية العناية بالأظافر والبشرة والشعر والاستشوار',
                'experience' => 'خبرة لأكثر من 7 سنوات',
                'avatar' => '/images/specialists/jenky.png',
                'bio' => 'متخصصة في خدمات العناية بالأظافر والبشرة والشعر، وتتميز بمهارتها في الاستشوار بمختلف أنواع الويفي.',
                'is_home' => true,
            ],
            'sp_ruth' => [
                'name_ar' => 'روث',
                'name_en' => 'Ruth',
                'role' => 'أخصائية العناية بالأظافر وتقنيات المساج',
                'experience' => 'خبرة لأكثر من 12 عامًا',
                'avatar' => '/images/specialists/ruth.png',
                'bio' => 'تمتع بخبرة واسعة في العناية بالأظافر والمساج ، حاصلة على شهادات دولية متخصصة في تقنيات المساج.',
                'is_home' => true,
            ],
        ];

        $specialistModels = [];
        $order = 1;

        foreach ($specialists as $slug => $sp) {
            $specialistModel = Specialist::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => [
                        'ar' => $sp['name_ar'],
                        'en' => $sp['name_en'],
                    ],
                    'role' => [
                        'ar' => $sp['role'],
                        'en' => 'Certified Specialist',
                    ],
                    'experience' => [
                        'ar' => $sp['experience'],
                        'en' => '10+ Years Experience',
                    ],
                    'bio' => [
                        'ar' => $sp['bio'],
                        'en' => 'Professional certified experience at Hairline Lounge.',
                    ],
                    'avatar_url' => $sp['avatar'],
                    'phone' => '0548857705',
                    'is_home_specialist' => $sp['is_home'],
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );

            $specialistModels[$slug] = $specialistModel;

            // Seed 7 days shifts
            for ($day = 0; $day <= 6; $day++) {
                SpecialistShift::updateOrCreate(
                    [
                        'specialist_id' => $specialistModel->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => '14:00:00',
                        'end_time' => '22:00:00',
                        'is_day_off' => false,
                    ]
                );
            }
        }

        // Link services to specialists
        $allServices = Service::all();
        if ($allServices->isNotEmpty()) {
            foreach ($specialistModels as $sp) {
                // Attach a subset of services to each specialist
                $sampleServices = $allServices->random(min(15, $allServices->count()));
                foreach ($sampleServices as $srv) {
                    $sp->services()->syncWithoutDetaching([
                        $srv->id => ['custom_duration_minutes' => $srv->duration_minutes],
                    ]);
                }
            }
        }
    }
}
