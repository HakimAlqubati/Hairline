<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FounderStory;
use App\Models\HeroSlide;
use App\Models\SalonGallery;
use App\Models\SalonSetting;
use App\Models\SalonWorkingHour;
use Illuminate\Database\Seeder;

class SalonSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Salon Settings
        SalonSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => [
                    'ar' => 'هيرلاين',
                    'en' => 'HAIRLINE',
                ],
                'tagline' => [
                    'ar' => 'صالون هيرلاين النسائي',
                    'en' => 'BEAUTY SALON',
                ],
                'description_line1' => [
                    'ar' => 'وجهتكِ الفاخرة المتكاملة للعناية بالشعر والأظافر والجمال،',
                    'en' => 'Your premier destination for luxury hair care, nails, and beauty.',
                ],
                'description_line2' => [
                    'ar' => 'بأيدي خبيرات محترفات وأرقى المنتجات العالمية في أجواء ملكية.',
                    'en' => 'Delivered by certified specialists with world-class products in a royal ambiance.',
                ],
                'address' => [
                    'ar' => 'الرياض - حي المصيف، مخرج 5، شارع زهير بن أبي سلمى',
                    'en' => 'Riyadh - Al-Masyaf District, Exit 5, Zuhair Bin Abi Sulma St',
                ],
                'logo_path' => '/images/logo.png',
                'phone' => '0548857705',
                'whatsapp' => '966548857705',
                'instagram' => 'hairline.sa',
                'tiktok' => 'hairline.sa',
                'snapchat' => 'hairline.sa',
                'email' => 'info@hairlinesalon.sa',
                'latitude' => 24.7677,
                'longitude' => 46.6896,
                'google_maps_url' => 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps',
                'tax_number' => '300998877660003',
                'vat_percentage' => 15.00,
                'stats' => [
                    ['value' => '+12', 'label' => 'أخصائية محترفة', 'bg' => 'bg-sage-soft', 'text' => 'text-sage-dark'],
                    ['value' => '100%', 'label' => 'منتجات أصلية', 'bg' => 'bg-gold-light', 'text' => 'text-gold-dark'],
                ],
            ]
        );

        // 2. Hero Slides (4 slides)
        $slides = [
            [
                'img' => '/images/salon/reception-desk.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80',
                'title' => 'هير لاين لأن الجمال إحساس',
                'subtitle' => 'اهتمامك يستحق عناية احترافية على يد أخصائيات خبيرات.',
            ],
            [
                'img' => '/images/salon/hair-styling-chairs.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1000&q=80',
                'title' => 'شعرك عنوان إطلالتك',
                'subtitle' => 'لأن جمال شعرك يبدأ من جودة العناية نمنحك عناية عالية الجودة تمنحك الرضا في كل زيارة.',
            ],
            [
                'img' => '/images/salon/salon-stations-tv.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=1000&q=80',
                'title' => 'استثمري في جمال شعرك',
                'subtitle' => 'مع اشتراكات هير لاين الدورية التي تجمع لك الجلسات والباقات المصممة لك.',
            ],
            [
                'img' => '/images/salon/salon-screen-display.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80',
                'title' => 'عناية تبدأ بفهم طبيعة شعرك',
                'subtitle' => 'خلطات عضوية، معالجات مغذية، وحلول متنوعة، نختار منها ما يناسب احتياج شعرك.',
            ],
        ];

        foreach ($slides as $index => $slide) {
            HeroSlide::updateOrCreate(
                ['image_path' => $slide['img']],
                [
                    'title' => [
                        'ar' => $slide['title'],
                        'en' => 'HAIRLINE BEAUTY LOUNGE',
                    ],
                    'subtitle' => [
                        'ar' => $slide['subtitle'],
                        'en' => 'Professional hair and beauty treatments by certified experts.',
                    ],
                    'badge_text' => [
                        'ar' => 'صالون هيرلاين النسائي',
                        'en' => 'HAIRLINE BEAUTY SALON',
                    ],
                    'fallback_url' => $slide['fallback'] ?? null,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }

        // 3. Salon Galleries (5 galleries)
        $galleries = [
            [
                'id' => 'reception',
                'title' => 'الاستقبال الملكي',
                'subtitle' => 'شعار هيرلاين المذهب ومنطقة الضيافة وخدمة العملاء',
                'image_url' => '/images/salon/reception-desk.jpg',
                'aspect_ratio' => '16/10',
                'tag' => 'منطقة الاستقبال',
            ],
            [
                'id' => 'styling_chairs',
                'title' => 'محطات التصفيف الفاخرة',
                'subtitle' => 'مرايا بإضاءة نهارية سينمائية وجدار الورود الطبيعية',
                'image_url' => '/images/salon/hair-styling-chairs.jpg',
                'aspect_ratio' => '3/4',
                'tag' => 'تصفيف الشعر',
            ],
            [
                'id' => 'stations_tv',
                'title' => 'أجواء اللوج الفندقية',
                'subtitle' => 'شاشات عرض الموديلات وأحدث صيحات التجميل والعناية',
                'image_url' => '/images/salon/salon-stations-tv.jpg',
                'aspect_ratio' => '16/10',
                'tag' => 'الصالون الداخلي',
            ],
            [
                'id' => 'screen_display',
                'title' => 'ركن الجمال والخصوصية',
                'subtitle' => 'فواصل خصوصية تامة وخدمة متفردة لكل عميلة',
                'image_url' => '/images/salon/salon-screen-display.jpg',
                'aspect_ratio' => '16/10',
                'tag' => 'خصوصية تامة',
            ],
            [
                'id' => 'business_cards',
                'title' => 'هوية وتفاصيل الفخامة',
                'subtitle' => 'بطاقات العضوية وخدمة الحجز المباشر 0548857705',
                'image_url' => '/images/salon/business-cards.jpg',
                'aspect_ratio' => '3/4',
                'tag' => 'هوية هيرلاين',
            ],
        ];

        foreach ($galleries as $index => $item) {
            SalonGallery::updateOrCreate(
                ['slug' => $item['id']],
                [
                    'title' => [
                        'ar' => $item['title'],
                        'en' => strtoupper(str_replace('_', ' ', $item['id'])),
                    ],
                    'subtitle' => [
                        'ar' => $item['subtitle'],
                        'en' => 'Luxury facilities designed for ultimate privacy and comfort.',
                    ],
                    'tag' => [
                        'ar' => $item['tag'],
                        'en' => 'Facilities',
                    ],
                    'image_path' => $item['image_url'],
                    'aspect_ratio' => $item['aspect_ratio'] ?? '16/10',
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }

        // 4. Founder Story
        FounderStory::updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'ar' => 'من نحن',
                    'en' => 'About Hairline',
                ],
                'founder_name' => [
                    'ar' => 'سارة السيف',
                    'en' => 'Sara Al-Saif',
                ],
                'founder_title' => [
                    'ar' => 'مؤسسة هير لاين',
                    'en' => 'Founder of Hairline',
                ],
                'intro_text' => [
                    'ar' => 'تأسس هير لاين عام 2020 برؤية وشغف من مؤسسته سارة السيف، ليكون وجهتكِ المفضلة للجمال والعناية.',
                    'en' => 'Founded in 2020 with passion and vision to be your sanctuary of beauty and wellness.',
                ],
                'description_text' => [
                    'ar' => 'هير لاين هو مساحتكِ الخاصة للدلال والتجدد، نهتم بأدق التفاصيل ونقدم لكِ خدمات مميزة في عالم العناية بالشعر والبشرة والأظافر، هدفنا أن تعيشي أحلى تجربة من أول زيارة.',
                    'en' => 'Hairline is your personal space for pampering and renewal with meticulous attention to detail.',
                ],
                'belief_text' => [
                    'ar' => 'نؤمن إن الجمال الحقيقي يبدأ من إحساسكِ الجيد بنفسكِ وأن الاهتمام بذاتكِ ليس مجرد رفاهية، بل جزء من حياة كل سيدة، ومن هنا، نحرص على تقديم تجربة عناية متكاملة تمنحكِ لحظات تستحقينها.',
                    'en' => 'We believe true beauty begins from feeling confident and well cared for.',
                ],
                'founder_message_title' => [
                    'ar' => 'كلمة المؤسسة',
                    'en' => "Founder's Message",
                ],
                'founder_message_paragraphs' => [
                    'ar' => json_encode([
                        'من يوم تأسيس هيرلاين، كان طموحي أقدم أكثر من مجرد خدمات صالون، كنت أبغى أصنع مساحة خاصة لكل سيدة تحس فيها بالاهتمام بنفسها والراحة.',
                        'أؤمن بأن الجمال يتجاوز المظهر، هو انعكاس للشعور بالثقة بالنفس والرضا الداخلي، وكل امرأه تستحق وقت لنفسها يعزز إحساسها بجمالها وقوتها وتميزها.',
                        'ومن هنا جاء هير لاين عشان يكون جزء من رحلتي ورحلتكم بالاهتمام بالجمال والمظهر وتعزيز الثقة الداخلية وتقديم خدمة تستحقها كل امرأه بمختلف الأعمار.',
                    ], JSON_UNESCAPED_UNICODE),
                    'en' => json_encode([
                        'From the founding day of Hairline, our ambition was to create more than just salon services.',
                        'We believe beauty reflects self-confidence and inner peace.',
                        'Hairline was created to be part of every woman\'s self-care journey.',
                    ]),
                ],
                'founding_year' => 2020,
                'founder_image_path' => '/images/salon/salon-stations-tv.jpg',
                'is_active' => true,
            ]
        );

        // 5. Working Hours (7 days)
        for ($day = 0; $day <= 6; $day++) {
            $dayNamesAr = [
                0 => 'الأحد',
                1 => 'الاثنين',
                2 => 'الثلاثاء',
                3 => 'الأربعاء',
                4 => 'الخميس',
                5 => 'الجمعة',
                6 => 'السبت',
            ];
            $dayNamesEn = [
                0 => 'Sunday',
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
            ];

            SalonWorkingHour::updateOrCreate(
                ['day_of_week' => $day],
                [
                    'day_label' => [
                        'ar' => $dayNamesAr[$day],
                        'en' => $dayNamesEn[$day],
                    ],
                    'opening_time' => '14:00:00',
                    'closing_time' => '22:00:00',
                    'is_closed' => false,
                    'notes' => [
                        'ar' => 'من 2:00 ظهراً حتى 10:00 مساءً',
                        'en' => '2:00 PM to 10:00 PM daily',
                    ],
                ]
            );
        }
    }
}
