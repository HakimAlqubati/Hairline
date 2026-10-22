<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\HeroSlide;
use App\Models\SalonGallery;
use App\Models\SalonSetting;
use App\Models\SalonWorkingHour;
use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Provides database-backed and cached data for the salon's main identity,
 * working hours, location, and contact information.
 */
class SalonInfoDataService
{
    public function __construct(
        private readonly ?SpecialistsDataService $specialistsService = null
    ) {}

    /**
     * @return array{
     *     name_en: string,
     *     name_ar: string,
     *     tagline_en: string,
     *     tagline_ar: string,
     *     description_line1: string,
     *     description_line2: string,
     *     hero_slogans: string[],
     *     logo_url: string,
     *     stats: array<int, array{value: string, label: string, bg: string, text: string}>,
     *     hero_slides: array<int, array{img: string, fallback: string, title: string, subtitle: string}>,
     *     hero_images: array<int, array{url: string, alt: string, fallback: string}>
     * }
     */
    public function getSalonIdentity(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_SALON_IDENTITY, function () {
            $setting = SalonSetting::first();
            $slides = HeroSlide::where('is_active', true)->orderBy('sort_order')->get();

            $slidesArray = [];
            foreach ($slides as $slide) {
                $slidesArray[] = [
                    'img' => $slide->image_path,
                    'fallback' => $slide->fallback_url ?: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80',
                    'title' => $slide->getTranslation('title', 'ar'),
                    'subtitle' => $slide->getTranslation('subtitle', 'ar') ?: '',
                ];
            }

            return [
                'name_en' => $setting?->getTranslation('name', 'en') ?: 'HAIRLINE',
                'name_ar' => $setting?->getTranslation('name', 'ar') ?: 'هيرلاين',
                'tagline_en' => $setting?->getTranslation('tagline', 'en') ?: 'BEAUTY SALON',
                'tagline_ar' => $setting?->getTranslation('tagline', 'ar') ?: 'صالون هيرلاين النسائي',
                'description_line1' => $setting?->getTranslation('description_line1', 'ar') ?: 'وجهتكِ الفاخرة المتكاملة للعناية بالشعر والأظافر والجمال،',
                'description_line2' => $setting?->getTranslation('description_line2', 'ar') ?: 'بأيدي خبيرات محترفات وأرقى المنتجات العالمية في أجواء ملكية.',
                'hero_slogans' => [
                    'فخامة الجمال',
                    'أناقتكِ تتجدد',
                ],
                'logo_url' => $setting?->logo_path ?: '/images/logo.png',
                'stats' => $this->getStats($setting),
                'hero_slides' => ! empty($slidesArray) ? $slidesArray : [
                    [
                        'img' => '/images/salon/reception-desk.jpg',
                        'fallback' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80',
                        'title' => 'هير لاين لأن الجمال إحساس',
                        'subtitle' => 'اهتمامك يستحق عناية احترافية على يد أخصائيات خبيرات.',
                    ],
                ],
                'hero_images' => [
                    [
                        'url' => '/images/salon/reception-desk.jpg',
                        'alt' => 'منطقة الاستقبال والترحيب الفاخرة في صالون هيرلاين',
                        'fallback' => 'https://placehold.co/800x1000/E2EFEA/4A5D54?text=HAIRLINE+LOUNGE',
                    ],
                    [
                        'url' => '/images/salon/hair-styling-chairs.jpg',
                        'alt' => 'محطات تصفيف الشعر الفاخرة وجدار الورود في هيرلاين',
                        'fallback' => 'https://placehold.co/800x1000/F5EFE6/C5A373?text=HAIRLINE+BEAUTY',
                    ],
                ],
            ];
        });
    }

    /**
     * @return array<int, array{value: string, label: string, bg: string, text: string}>
     */
    public function getStats(?SalonSetting $setting = null): array
    {
        $setting ??= SalonSetting::first();
        $specialists = $this->specialistsService ?? app(SpecialistsDataService::class);
        $formattedCount = $specialists->getFormattedSpecialistsCount();

        $rawStats = $setting?->stats ?: [
            ['value' => $formattedCount, 'label' => 'أخصائية محترفة', 'bg' => 'bg-sage-soft', 'text' => 'text-sage-dark'],
            ['value' => '100%', 'label' => 'منتجات أصلية', 'bg' => 'bg-gold-light', 'text' => 'text-gold-dark'],
        ];

        $hasSpecialistStat = false;
        $stats = [];
        foreach ($rawStats as $stat) {
            if (isset($stat['label']) && (str_contains((string) $stat['label'], 'أخصائ') || stripos((string) $stat['label'], 'specialist') !== false)) {
                $stat['value'] = $formattedCount;
                $hasSpecialistStat = true;
            }
            $stats[] = $stat;
        }

        if (! $hasSpecialistStat) {
            array_unshift($stats, [
                'value' => $formattedCount,
                'label' => 'أخصائية محترفة',
                'bg' => 'bg-sage-soft',
                'text' => 'text-sage-dark',
            ]);
        }

        return $stats;
    }

    /**
     * @return array<int, array{
     *     id: string,
     *     title: string,
     *     subtitle: string,
     *     image_url: string,
     *     aspect_ratio: string,
     *     tag: string
     * }>
     */
    public function getSalonGallery(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_SALON_GALLERY, function () {
            $galleries = SalonGallery::where('is_active', true)->orderBy('sort_order')->get();

            if ($galleries->isEmpty()) {
                return [];
            }

            return $galleries->map(fn ($g) => [
                'id' => $g->slug,
                'title' => $g->getTranslation('title', 'ar'),
                'subtitle' => $g->getTranslation('subtitle', 'ar') ?: '',
                'image_url' => $g->image_path,
                'aspect_ratio' => $g->aspect_ratio,
                'tag' => $g->getTranslation('tag', 'ar') ?: '',
            ])->toArray();
        });
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
        $cachedHours = Cache::rememberForever(HairlineCacheService::KEY_WORKING_HOURS, function () {
            $hour = SalonWorkingHour::where('is_closed', false)->first();

            return [
                'days' => 'يومياً',
                'hours_ar' => $hour ? $hour->getTranslation('notes', 'ar') : 'من 2:00 ظهراً حتى 10:00 مساءً',
                'hours_from' => $hour ? substr((string) $hour->opening_time, 0, 5) : '14:00',
                'hours_to' => $hour ? substr((string) $hour->closing_time, 0, 5) : '22:00',
            ];
        });

        return array_merge($cachedHours, [
            'is_open_now' => $this->checkIsOpenNow(),
        ]);
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
        $setting = SalonSetting::first();

        $address = $setting?->getTranslation('address', 'ar') ?? 'الرياض - حي المصيف، مخرج 5، شارع زهير بن أبي سلمى';
        $gmaps = $setting?->google_maps_url ?? 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps';
        $lat = (float) ($setting?->latitude ?? 24.7677);
        $lng = (float) ($setting?->longitude ?? 46.6896);

        return [
            'city' => 'الرياض',
            'district' => 'حي المصيف',
            'street' => 'مخرج 5',
            'full_address_ar' => $address,
            'parking_note' => 'مواقف خاصة متوفرة أمام الصالون مباشرة',
            'google_maps_url' => $gmaps,
            'coordinates' => [
                'lat' => $lat,
                'lng' => $lng,
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
        $setting = SalonSetting::first();

        $wa = $setting?->whatsapp ?: '966548857705';
        $ig = $setting?->instagram ?: 'hairline.5';
        $sc = $setting?->snapchat ?: 'hairline.5';
        $tt = $setting?->tiktok ?: 'hairline';

        return [
            'whatsapp' => 'https://wa.me/'.$wa,
            'whatsapp_display' => '054 885 7705',
            'instagram' => 'https://www.instagram.com/'.$ig,
            'instagram_handle' => '@'.$ig,
            'snapchat' => 'https://www.snapchat.com/add/'.$sc,
            'snapchat_handle' => '@'.$sc,
            'tiktok' => 'https://www.tiktok.com/@'.$tt,
            'tiktok_handle' => '@'.$tt,
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
        $openMinutes = 14 * 60;       // 14:00 (2:00 PM)
        $closeMinutes = 22 * 60;      // 22:00 (10:00 PM)

        return $currentMinutes >= $openMinutes && $currentMinutes <= $closeMinutes;
    }
}
