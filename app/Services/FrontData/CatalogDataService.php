<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Database-backed & cached service catalog for Hairline Beauty Lounge.
 */
class CatalogDataService
{
    /**
     * Return all official service categories with their complete sub-services (Cached).
     *
     * @return array<int, array{
     *     id: string,
     *     title: string,
     *     title_en: string,
     *     subtitle: string,
     *     badge_count: string,
     *     icon: string,
     *     page_ref: int,
     *     services: array<int, array{
     *         id: string,
     *         name: string,
     *         name_en: string,
     *         group: string,
     *         length_tier: ?string,
     *         length_label: ?string,
     *         description: string,
     *         duration: string,
     *         duration_min: int,
     *         price: int,
     *         price_display: ?string,
     *         currency: string,
     *         is_popular: bool,
     *         is_bookable: bool,
     *         is_home_available: bool,
     *         inclusions?: ?string
     *     }>
     * }>
     */
    public function getAllCategories(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_ALL_CATEGORIES, function () {
            $categories = ServiceCategory::with([
                'services' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'services.group',
                'services.pricingTiers.hairLength',
            ])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            return $categories->map(function (ServiceCategory $cat) {
                $services = $cat->services->map(function (Service $srv) {
                    $firstTier = $srv->pricingTiers->first();
                    $lengthCode = $firstTier?->hairLength?->code;
                    $lengthLabel = $firstTier?->getTranslation('custom_label', 'ar');

                    $priceDisplay = $srv->getTranslation('price_display_override', 'ar');
                    if (! $priceDisplay && $srv->base_price > 0) {
                        $priceDisplay = ((int) $srv->base_price).' ر.س';
                    }

                    return [
                        'id' => $srv->code,
                        'name' => $srv->getTranslation('name', 'ar'),
                        'name_en' => $srv->getTranslation('name', 'en'),
                        'group' => $srv->group?->getTranslation('name', 'ar') ?? 'الخدمات العامة',
                        'length_tier' => $lengthCode,
                        'length_label' => $lengthLabel,
                        'description' => $srv->getTranslation('description', 'ar') ?? '',
                        'duration' => "{$srv->duration_minutes} دقيقة",
                        'duration_min' => $srv->duration_minutes,
                        'price' => (int) $srv->base_price,
                        'price_display' => $priceDisplay,
                        'currency' => 'SAR',
                        'is_popular' => (bool) $srv->is_popular,
                        'is_bookable' => (bool) $srv->is_bookable,
                        'is_home_available' => (bool) $srv->is_home_available,
                        'inclusions' => $srv->getTranslation('inclusions', 'ar'),
                    ];
                })->all();

                return [
                    'id' => $cat->slug,
                    'title' => $cat->getTranslation('name', 'ar'),
                    'title_en' => $cat->getTranslation('name', 'en'),
                    'subtitle' => $cat->getTranslation('subtitle', 'ar') ?? '',
                    'badge_count' => count($services).' خدمة',
                    'icon' => $cat->icon ?? 'scissors',
                    'page_ref' => $cat->page_ref ?? 1,
                    'services' => $services,
                ];
            })->all();
        });
    }

    /**
     * Find a service by its unique code ID.
     */
    public function findServiceById(string $serviceId): ?array
    {
        $all = $this->getAllCategories();
        foreach ($all as $cat) {
            foreach ($cat['services'] as $srv) {
                if ($srv['id'] === $serviceId) {
                    return $srv;
                }
            }
        }

        return null;
    }

    /**
     * Normalize Arabic/English text and Eastern numerals for unified search matching.
     */
    public function normalizeSearchText(string $text): string
    {
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $normalized = str_replace($eastern, $western, mb_strtolower($text));
        $normalized = (string) preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}]/u', '', $normalized);
        $normalized = str_replace(
            ['أ', 'إ', 'آ', 'ٱ', 'ة', 'ى', 'ؤ', 'ئ'],
            ['ا', 'ا', 'ا', 'ا', 'ه', 'ي', 'و', 'ي'],
            $normalized
        );

        return trim((string) preg_replace('/\s+/u', ' ', $normalized));
    }

    /**
     * Search services by Arabic/English name, category, group, price, or description using unified token matching.
     */
    public function searchServices(string $query): array
    {
        $clean = $this->normalizeSearchText($query);
        if ($clean === '') {
            return [];
        }

        $tokens = array_values(array_filter(explode(' ', $clean), fn (string $t): bool => $t !== ''));
        $results = [];
        $all = $this->getAllCategories();

        foreach ($all as $cat) {
            foreach ($cat['services'] as $srv) {
                $haystack = $this->normalizeSearchText(implode(' ', [
                    $cat['title'] ?? '',
                    $cat['title_en'] ?? '',
                    $srv['name'] ?? '',
                    $srv['name_en'] ?? '',
                    $srv['description'] ?? '',
                    $srv['group'] ?? '',
                    (string) ($srv['price'] ?? ''),
                    (string) ($srv['price_display'] ?? ''),
                ]));

                $allMatched = true;
                foreach ($tokens as $token) {
                    if (! str_contains($haystack, $token)) {
                        $allMatched = false;
                        break;
                    }
                }

                if ($allMatched) {
                    $results[] = $srv;
                }
            }
        }

        return $results;
    }

    /**
     * Calculate total price and count for selected service IDs.
     */
    public function calculateTotals(array $serviceIds): array
    {
        $total = 0;
        $count = 0;
        foreach ($serviceIds as $id) {
            $srv = $this->findServiceById($id);
            if ($srv) {
                $total += $srv['price'];
                $count++;
            }
        }

        return [
            'total_price' => $total,
            'count' => $count,
            'currency' => 'SAR',
        ];
    }

    /**
     * Check if a service is available for online booking.
     */
    public function isServiceBookable(string $serviceId): bool
    {
        $categories = $this->getBookableCategoriesWithSpecialists();
        foreach ($categories as $cat) {
            foreach ($cat['subServices'] as $srv) {
                if ($srv['id'] === $serviceId) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns bookable categories and services with their assigned specialists (Cached).
     *
     * @return array<int, array{
     *     id: string,
     *     title: string,
     *     title_en: string,
     *     badge: string,
     *     icon: string,
     *     description: string,
     *     subServices: array<int, array{
     *         id: string,
     *         name: string,
     *         desc: string,
     *         duration: string,
     *         price: string,
     *         is_bookable: bool,
     *         is_home_available: bool,
     *         specialists: array<int, string>
     *     }>
     * }>
     */
    public function getBookableCategoriesWithSpecialists(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_BOOKABLE_CATEGORIES, function () {
            $categories = ServiceCategory::with([
                'services' => function ($q) {
                    $q->where('is_active', true)
                        ->where('is_bookable', true)
                        ->whereHas('specialists')
                        ->orderBy('sort_order');
                },
                'services.specialists',
            ])->where('is_active', true)->orderBy('sort_order')->get();

            $result = [];
            foreach ($categories as $cat) {
                if ($cat->services->isEmpty()) {
                    continue;
                }

                $subServices = [];
                foreach ($cat->services as $srv) {
                    $override = $srv->getTranslation('price_display_override', 'ar');
                    if ($override) {
                        if (preg_match('/(\d+)\s*-\s*(\d+)/', $override, $m)) {
                            $priceHint = "يبدأ من {$m[1]} إلى {$m[2]} ر.س بحسب الأطوال والكثافة";
                        } elseif (preg_match('/(\d+)/', $override, $m)) {
                            $priceHint = "يبدأ من {$m[1]} ر.س بحسب الأطوال والكثافة";
                        } else {
                            $priceHint = str_replace('SR', 'ر.س', $override).' بحسب الأطوال والكثافة';
                        }
                    } else {
                        $priceHint = 'يبدأ من '.((int) $srv->base_price).' ر.س بحسب الأطوال والكثافة';
                    }

                    $subServices[] = [
                        'id' => $srv->code,
                        'name' => $srv->getTranslation('name', 'ar'),
                        'desc' => $srv->getTranslation('description', 'ar') ?? '',
                        'duration' => "{$srv->duration_minutes} دقيقة",
                        'price' => "{$srv->base_price} ر.س",
                        'price_hint' => $priceHint,
                        'is_bookable' => (bool) $srv->is_bookable,
                        'is_home_available' => (bool) $srv->is_home_available,
                        'specialists' => $srv->specialists->pluck('slug')->toArray(),
                    ];
                }

                $result[] = [
                    'id' => $cat->slug,
                    'title' => $cat->getTranslation('name', 'ar'),
                    'title_en' => $cat->getTranslation('name', 'en'),
                    'badge' => $cat->getTranslation('subtitle', 'ar') ?? '',
                    'icon' => 'fa-'.$cat->icon,
                    'description' => $cat->getTranslation('subtitle', 'ar') ?? '',
                    'subServices' => $subServices,
                ];
            }

            return $result;
        });
    }

    /**
     * Returns popular walk-in services grouped by category (Cached).
     */
    public function getWalkInServicesGrouped(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_WALKIN_SERVICES, function () {
            $categories = ServiceCategory::with(['services' => function ($q) {
                $q->where('is_active', true)->where('is_popular', true)->orderBy('sort_order');
            }])->where('is_active', true)->orderBy('sort_order')->get();

            $result = [];
            foreach ($categories as $cat) {
                $items = [];
                foreach ($cat->services as $srv) {
                    $items[] = [
                        'name' => $srv->getTranslation('name', 'ar'),
                        'duration' => "{$srv->duration_minutes} دقيقة",
                        'price' => "{$srv->base_price} ر.س",
                        'tag' => 'زيارة فورية',
                    ];
                }

                if (! empty($items)) {
                    $result[] = [
                        'category' => $cat->getTranslation('name', 'ar'),
                        'items' => $items,
                    ];
                }
            }

            return $result;
        });
    }
}
