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
     * Search services by Arabic/English name, group or description.
     */
    public function searchServices(string $query): array
    {
        $clean = trim(mb_strtolower($query));
        if ($clean === '') {
            return [];
        }

        $results = [];
        $all = $this->getAllCategories();
        foreach ($all as $cat) {
            foreach ($cat['services'] as $srv) {
                if (
                    str_contains(mb_strtolower($srv['name']), $clean) ||
                    str_contains(mb_strtolower($srv['name_en']), $clean) ||
                    str_contains(mb_strtolower($srv['description']), $clean) ||
                    str_contains(mb_strtolower($srv['group']), $clean)
                ) {
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
     *         specialists: array<int, string>
     *     }>
     * }>
     */
    public function getBookableCategoriesWithSpecialists(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_BOOKABLE_CATEGORIES, function () {
            $categories = ServiceCategory::with([
                'services' => function ($q) {
                    $q->where('is_active', true)->whereHas('specialists');
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
                    $subServices[] = [
                        'id' => $srv->code,
                        'name' => $srv->getTranslation('name', 'ar'),
                        'desc' => $srv->getTranslation('description', 'ar') ?? '',
                        'duration' => "{$srv->duration_minutes} دقيقة",
                        'price' => "{$srv->base_price} ر.س",
                        'is_bookable' => true,
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
