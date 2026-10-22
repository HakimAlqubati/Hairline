<?php

declare(strict_types=1);

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

class HairlineCacheService
{
    public const TTL_ONE_DAY = 86400; // 24 hours

    public const KEY_SALON_IDENTITY = 'hairline:salon:identity:v2';

    public const KEY_HERO_SLIDES = 'hairline:salon:hero_slides';

    public const KEY_SALON_GALLERY = 'hairline:salon:gallery';

    public const KEY_ABOUT_US = 'hairline:salon:about_us';

    public const KEY_WORKING_HOURS = 'hairline:salon:working_hours';

    public const KEY_CATALOG_MENU_PAGES = 'hairline:catalog:menu_pages';

    public const KEY_WALKIN_SERVICES = 'hairline:catalog:walkin_services';

    public const KEY_BOOKABLE_CATEGORIES = 'hairline:catalog:bookable_categories';

    public const KEY_ALL_CATEGORIES = 'hairline:catalog:all_categories';

    public const KEY_SPECIALISTS_ALL = 'hairline:specialists:all';

    public const KEY_SPECIALISTS_COUNT = 'hairline:specialists:count';

    public const KEY_DISTRICTS = 'hairline:home_service:districts';

    /**
     * Clear all cached hairline front data.
     */
    public static function clearAll(): void
    {
        self::clearSalonInfo();
        self::clearCatalog();
        self::clearSpecialists();
        self::clearHomeServices();
    }

    /**
     * Clear salon info and about section cache.
     */
    public static function clearSalonInfo(): void
    {
        Cache::forget(self::KEY_SALON_IDENTITY);
        Cache::forget(self::KEY_HERO_SLIDES);
        Cache::forget(self::KEY_SALON_GALLERY);
        Cache::forget(self::KEY_ABOUT_US);
        Cache::forget(self::KEY_WORKING_HOURS);
    }

    /**
     * Clear catalog, services and menu pages cache.
     */
    public static function clearCatalog(): void
    {
        Cache::forget(self::KEY_CATALOG_MENU_PAGES);
        Cache::forget(self::KEY_WALKIN_SERVICES);
        Cache::forget(self::KEY_BOOKABLE_CATEGORIES);
        Cache::forget(self::KEY_ALL_CATEGORIES);
    }

    /**
     * Clear specialists cache.
     */
    public static function clearSpecialists(): void
    {
        Cache::forget(self::KEY_SPECIALISTS_ALL);
        Cache::forget(self::KEY_SPECIALISTS_COUNT);
        Cache::forget(self::KEY_SALON_IDENTITY);
        Cache::forget(self::KEY_BOOKABLE_CATEGORIES);
    }

    /**
     * Clear home service districts cache.
     */
    public static function clearHomeServices(): void
    {
        Cache::forget(self::KEY_DISTRICTS);
    }
}
