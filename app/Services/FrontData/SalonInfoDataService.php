<?php

declare(strict_types=1);

namespace App\Services\FrontData;

/**
 * Provides static data for the salon's main identity, about section,
 * working hours, location, and contact information.
 *
 * All data is returned as typed arrays/value objects for easy replacement
 * with a real data source in the future.
 */
class SalonInfoDataService
{
    /**
     * @return array{
     *     name_en: string,
     *     name_ar: string,
     *     tagline_en: string,
     *     tagline_ar: string,
     *     description_line1: string,
     *     description_line2: string,
     *     hero_slogans: string[],
     *     hero_images: array<int, array{url: string, alt: string, fallback: string}>
     * }
     */
    public function getSalonIdentity(): array
    {
        return [
            'name_en' => 'HAIRLINE',
            'name_ar' => 'هيرلاين',
            'tagline_en' => 'BEAUTY LOUNGE',
            'tagline_ar' => 'صالون هيرلاين النسائي',
            'description_line1' => 'وجهتكِ الفاخرة المتكاملة للعناية بالشعر والأظافر والجمال،',
            'description_line2' => 'بأيدي خبيرات محترفات وأرقى المنتجات العالمية في أجواء ملكية.',
            'hero_slogans' => [
                'فخامة الجمال',
                'أناقتكِ تتجدد',
            ],
            'hero_images' => [
                [
                    'url' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=85',
                    'alt' => 'Hairline Beauty Lounge Luxury Portrait',
                    'fallback' => 'https://placehold.co/800x1000/E2EFEA/4A5D54?text=HAIRLINE+LOUNGE',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1000&q=85',
                    'alt' => 'Hairline Hair & Spa',
                    'fallback' => 'https://placehold.co/800x1000/F5EFE6/C5A373?text=HAIRLINE+BEAUTY',
                ],
            ],
        ];
    }

    /**
     * @return array{
     *     days: string,
     *     hours_ar: string,
     *     hours_from: string,
     *     hours_to: string,
     *     is_open_now: bool
     * }
     */
    public function getWorkingHours(): array
    {
        return [
            'days' => 'يومياً',
            'hours_ar' => '02:00 ظهراً – 10:30 مساءً',
            'hours_from' => '14:00',
            'hours_to' => '22:30',
            'is_open_now' => $this->checkIsOpenNow(),
        ];
    }

    /**
     * @return array{
     *     city: string,
     *     district: string,
     *     street: string,
     *     full_address_ar: string,
     *     parking_note: string,
     *     google_maps_url: string,
     *     coordinates: array{lat: float, lng: float}
     * }
     */
    public function getLocation(): array
    {
        return [
            'city' => 'الرياض',
            'district' => 'حي الملقا',
            'street' => 'طريق أنس بن مالك',
            'full_address_ar' => 'الرياض - حي الملقا، طريق أنس بن مالك',
            'parking_note' => 'مواقف خاصة متوفرة أمام الصالون مباشرة',
            'google_maps_url' => 'https://maps.google.com/?q=Riyadh+Al+Malqa',
            'coordinates' => [
                'lat' => 24.7880,
                'lng' => 46.6352,
            ],
        ];
    }

    /**
     * @return array{
     *     whatsapp: string,
     *     whatsapp_display: string,
     *     instagram: string,
     *     instagram_handle: string,
     *     snapchat: string,
     *     snapchat_handle: string,
     *     tiktok: string,
     *     tiktok_handle: string
     * }
     */
    public function getContactLinks(): array
    {
        return [
            'whatsapp' => 'https://wa.me/966500000000',
            'whatsapp_display' => '050 000 0000',
            'instagram' => 'https://www.instagram.com/hairlinelounge',
            'instagram_handle' => '@hairlinelounge',
            'snapchat' => 'https://www.snapchat.com/add/hairlinelounge',
            'snapchat_handle' => '@hairlinelounge',
            'tiktok' => 'https://www.tiktok.com/@hairlinelounge',
            'tiktok_handle' => '@hairlinelounge',
        ];
    }

    /**
     * @return array<int, array{icon: string, title: string, description: string}>
     */
    public function getCompetitiveAdvantages(): array
    {
        return [
            [
                'icon' => 'award',
                'title' => 'خبيرات معتمدات دولياً',
                'description' => 'فريق من الخبيرات المحترفات الحاصلات على شهادات دولية في التجميل',
            ],
            [
                'icon' => 'sparkles',
                'title' => 'منتجات عالمية فاخرة',
                'description' => 'نستخدم حصرياً أرقى المنتجات العالمية كـ Olaplex وKerastase وNaomi',
            ],
            [
                'icon' => 'home',
                'title' => 'خدمة منزلية متميزة',
                'description' => 'نصلكِ في راحة بيتكِ في 12 حياً بشمال وشرق وغرب الرياض',
            ],
            [
                'icon' => 'shield',
                'title' => 'خصوصية تامة وأجواء ملكية',
                'description' => 'صالون نسائي خاص بنسبة 100% بأجواء هادئة وأنيقة تليق بكِ',
            ],
        ];
    }

    private function checkIsOpenNow(): bool
    {
        $now = now()->setTimezone('Asia/Riyadh');
        $hour = (int) $now->format('H');
        $min = (int) $now->format('i');

        $currentMinutes = $hour * 60 + $min;
        $openMinutes = 14 * 60;       // 14:00
        $closeMinutes = 22 * 60 + 30; // 22:30

        return $currentMinutes >= $openMinutes && $currentMinutes <= $closeMinutes;
    }
}
