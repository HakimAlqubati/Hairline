<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\HeroSlide;
use App\Models\SalonGallery;
use App\Models\SalonWorkingHour;
use App\Models\Setting;
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

            $siteNameAr = Setting::getSetting('site_name_ar', 'هيرلاين');
            $siteNameEn = Setting::getSetting('site_name_en', 'HAIRLINE');
            $siteTaglineAr = Setting::getSetting('site_tagline_ar', 'صالون هيرلاين النسائي');
            $siteTaglineEn = Setting::getSetting('site_tagline_en', 'BEAUTY SALON');
            $descriptionLine1 = Setting::getSetting('description_line1', 'وجهتكِ الفاخرة المتكاملة للعناية بالشعر والأظافر والجمال،');
            $descriptionLine2 = Setting::getSetting('description_line2', 'بأيدي خبيرات محترفات وأرقى المنتجات العالمية في أجواء ملكية.');
            $siteLogo = Setting::getSetting('site_logo') ?: '/images/logo.png';

            return [
                'name_en' => (string) $siteNameEn,
                'name_ar' => (string) $siteNameAr,
                'tagline_en' => (string) $siteTaglineEn,
                'tagline_ar' => (string) $siteTaglineAr,
                'description_line1' => (string) $descriptionLine1,
                'description_line2' => (string) $descriptionLine2,
                'hero_slogans' => [
                    'فخامة الجمال',
                    'أناقتكِ تتجدد',
                ],
                'logo_url' => (string) $siteLogo,
                'stats' => $this->getStats(),
                'hero_slides' => ! empty($slidesArray) ? $slidesArray : [
                    [
                        'img' => '/images/salon/reception-desk.jpg',
                        'fallback' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80',
                        'title' => 'هيرلاين لأن الجمال إحساس',
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
    public function getStats(): array
    {
        $specialists = $this->specialistsService ?? app(SpecialistsDataService::class);
        $formattedCount = $specialists->getFormattedSpecialistsCount();

        $statsSetting = Setting::getSetting('stats');
        $rawStats = null;
        if (is_string($statsSetting) && ! empty($statsSetting)) {
            $decoded = json_decode($statsSetting, true);
            if (is_array($decoded)) {
                $rawStats = $decoded;
            }
        } elseif (is_array($statsSetting)) {
            $rawStats = $statsSetting;
        }

        $rawStats = $rawStats ?: [
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
            $defaultWorkingHours = Setting::getSetting('default_working_hours');
            $hour = SalonWorkingHour::where('is_closed', false)->first();

            $hoursAr = $defaultWorkingHours ?: ($hour ? $hour->getTranslation('notes', 'ar') : 'من 2:00 ظهراً حتى 10:00 مساءً');

            return [
                'days' => 'يومياً',
                'hours_ar' => $hoursAr,
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
     *     google_maps_iframe: ?string,
     *     coordinates: array{lat: float, lng: float}
     * }
     */
    public function getLocation(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_SALON_LOCATION, function () {
            $address = Setting::getSetting('contact_address', 'الرياض - حي المصيف، مخرج 5، شارع زهير بن أبي سلمى');
            $gmaps = Setting::getSetting('google_maps_url', 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps');
            $customIframe = Setting::getSetting('google_maps_iframe');
            $lat = (float) (Setting::getSetting('latitude') ?: 24.7677);
            $lng = (float) (Setting::getSetting('longitude') ?: 46.6896);

            return [
                'city' => 'الرياض',
                'district' => 'حي المصيف',
                'street' => 'مخرج 5',
                'full_address_ar' => (string) $address,
                'parking_note' => 'مواقف خاصة متوفرة أمام الصالون مباشرة',
                'google_maps_url' => (string) $gmaps,
                'google_maps_iframe' => $customIframe ? (string) $customIframe : null,
                'coordinates' => [
                    'lat' => $lat,
                    'lng' => $lng,
                ],
            ];
        });
    }

    /**
     * @return array{
     *     whatsapp: string,
     *     whatsapp_display: string,
     *     phone: string,
     *     phone_2: ?string,
     *     email: ?string,
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
        return Cache::rememberForever(HairlineCacheService::KEY_CONTACT_LINKS, function () {
            $wa = Setting::getSetting('whatsapp_number', '966548857705');
            $phone = Setting::getSetting('contact_phone', '0548857705');
            $phone2 = Setting::getSetting('contact_phone_2');
            $email = Setting::getSetting('contact_email', 'info@hairlinesalon.sa');

            $igUrl = Setting::getSetting('instagram_url', 'https://www.instagram.com/Hairline.5');
            $igHandle = Setting::getSetting('instagram_handle', '@Hairline.5');

            $scUrl = Setting::getSetting('snapchat_url', 'https://www.snapchat.com/add/Hairline.5');
            $scHandle = Setting::getSetting('snapchat_handle', '@Hairline.5');

            $ttUrl = Setting::getSetting('tiktok_url', 'https://www.tiktok.com/@Hairline.TikTok');
            $ttHandle = Setting::getSetting('tiktok_handle', '@Hairline.TikTok');

            $cleanWa = preg_replace('/[^0-9]/', '', (string) $wa);

            $phoneDisplay = (string) $phone;
            if (strlen($phoneDisplay) === 10 && str_starts_with($phoneDisplay, '05')) {
                $phoneDisplay = substr($phoneDisplay, 0, 3).' '.substr($phoneDisplay, 3, 3).' '.substr($phoneDisplay, 6);
            }

            return [
                'whatsapp' => 'https://wa.me/'.$cleanWa,
                'whatsapp_display' => $phoneDisplay,
                'phone' => (string) $phone,
                'phone_2' => $phone2 ? (string) $phone2 : null,
                'email' => $email ? (string) $email : null,
                'instagram' => (string) $igUrl,
                'instagram_handle' => (string) $igHandle,
                'snapchat' => (string) $scUrl,
                'snapchat_handle' => (string) $scHandle,
                'tiktok' => (string) $ttUrl,
                'tiktok_handle' => (string) $ttHandle,
            ];
        });
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
