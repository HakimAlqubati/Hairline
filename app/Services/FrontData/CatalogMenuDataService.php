<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\HairLength;
use App\Models\Service;
use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Database-backed & cached catalog menu pages for Hairline Beauty Lounge.
 *
 * Provides the official 8-page menu enriched with live database records.
 */
class CatalogMenuDataService
{
    /**
     * Get all 8 official menu pages (Cached).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMenuPages(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_CATALOG_MENU_PAGES, function () {
            $dataFile = database_path('data/catalog_seed_data.json');
            $seedPages = [];
            if (file_exists($dataFile)) {
                $decoded = json_decode((string) file_get_contents($dataFile), true);
                $seedPages = $decoded['menu_pages'] ?? [];
            }

            // Load live services keyed by Arabic name for real-time pricing synchronization
            $liveServices = Service::with('pricingTiers.hairLength')
                ->where('is_active', true)
                ->get()
                ->keyBy(fn (Service $s) => $s->getTranslation('name', 'ar'));

            // Live hair lengths for Page 8
            $liveLengths = HairLength::where('is_active', true)
                ->orderBy('diagram_order')
                ->get()
                ->map(fn (HairLength $l) => [
                    'name_ar' => $l->getTranslation('name', 'ar'),
                    'name_en' => $l->getTranslation('name', 'en'),
                    'desc_ar' => $l->getTranslation('description', 'ar'),
                    'cm' => $l->approx_length_cm,
                    'number' => $l->diagram_order,
                ])
                ->all();

            $pages = [];
            foreach ($seedPages as $page) {
                $pageNumber = $page['page_number'] ?? 1;

                if ($pageNumber === 8) {
                    $page['lengths'] = ! empty($liveLengths) ? $liveLengths : ($page['lengths'] ?? []);
                    $pages[] = $page;

                    continue;
                }

                // Synchronize live prices for standard groups (Page 1)
                if (! empty($page['groups'])) {
                    foreach ($page['groups'] as &$group) {
                        if (! empty($group['items'])) {
                            foreach ($group['items'] as &$item) {
                                $arName = $item['ar_name'] ?? '';
                                if ($srv = $liveServices->get($arName)) {
                                    $item['price'] = $srv->getTranslation('price_display_override', 'ar') ?: ($srv->base_price > 0 ? ((int) $srv->base_price.' SR') : $item['price']);
                                }
                            }
                        }
                    }
                }

                // Synchronize live prices for subsections (Pages 2-7)
                if (! empty($page['subsections'])) {
                    foreach ($page['subsections'] as &$sub) {
                        if (! empty($sub['items'])) {
                            foreach ($sub['items'] as &$item) {
                                $arName = $item['ar_name'] ?? '';
                                if ($srv = $liveServices->get($arName)) {
                                    $item['price'] = $srv->getTranslation('price_display_override', 'ar') ?: ($srv->base_price > 0 ? ((int) $srv->base_price.' SR') : $item['price']);
                                }
                            }
                        }

                        if (! empty($sub['blocks'])) {
                            foreach ($sub['blocks'] as &$blk) {
                                $arName = $blk['ar_name'] ?? '';
                                if ($srv = $liveServices->get($arName)) {
                                    $tiersKeyed = $srv->pricingTiers->keyBy(fn ($t) => $t->getTranslation('custom_label', 'ar'));
                                    if (! empty($blk['lengths'])) {
                                        foreach ($blk['lengths'] as &$lenItem) {
                                            $lenAr = $lenItem['ar'] ?? '';
                                            if ($tier = $tiersKeyed->get($lenAr)) {
                                                $lenItem['price'] = ((int) $tier->price).' SR';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $pages[] = $page;
            }

            return $pages;
        });
    }
}
