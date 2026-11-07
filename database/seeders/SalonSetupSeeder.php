<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\SalonGallery;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SalonSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Settings
        $settings = [
            'site_name_ar' => 'هيرلاين',
            'site_name_en' => 'HAIRLINE',
            'site_tagline_ar' => 'صالون هيرلاين النسائي',
            'site_tagline_en' => 'BEAUTY SALON',
            'description_line1' => 'وجهتكِ الفاخرة المتكاملة للعناية بالشعر والأظافر والجمال،',
            'description_line2' => 'بأيدي خبيرات محترفات وأرقى المنتجات العالمية في أجواء ملكية.',
            'contact_address' => 'الرياض - حي المصيف، مخرج 5، شارع زهير بن أبي سلمى',
            'site_logo' => '/images/logo.png',
            'contact_phone' => '0548857705',
            'whatsapp_number' => '966548857705',
            'instagram_url' => 'https://www.instagram.com/Hairline.5',
            'instagram_handle' => '@Hairline.5',
            'tiktok_url' => 'https://www.tiktok.com/@Hairline.TikTok',
            'tiktok_handle' => '@Hairline.TikTok',
            'snapchat_url' => 'https://www.snapchat.com/add/Hairline.5',
            'snapchat_handle' => '@Hairline.5',
            'contact_email' => 'info@hairlinesalon.sa',
            'latitude' => '24.7677',
            'longitude' => '46.6896',
            'google_maps_url' => 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps',
            'tax_number' => '300998877660003',
            'vat_percentage' => '15',
            'stats' => json_encode([
                ['value' => '+12', 'label' => 'أخصائية محترفة', 'bg' => 'bg-sage-soft', 'text' => 'text-sage-dark'],
                ['value' => '100%', 'label' => 'منتجات أصلية', 'bg' => 'bg-gold-light', 'text' => 'text-gold-dark'],
            ], JSON_UNESCAPED_UNICODE),
        ];

        foreach ($settings as $key => $value) {
            Setting::setSetting($key, $value);
        }

        // 2. Hero Slides (4 slides)
        $slides = [
            [
                'img' => '/images/salon/reception-desk.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80',
                'title' => 'هيرلاين لأن الجمال إحساس',
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
                'subtitle' => 'مع اشتراكات هيرلاين الدورية التي تجمع لك الجلسات والباقات المصممة لك.',
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
                        'en' => 'HAIRLINE BEAUTY SALON',
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
    }
}
