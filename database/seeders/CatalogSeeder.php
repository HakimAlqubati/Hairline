<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CatalogPage;
use App\Models\HairLength;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceGroup;
use App\Models\ServicePricingTier;
use App\Models\Specialist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $dataFile = database_path('data/catalog_seed_data.json');
        if (! file_exists($dataFile)) {
            return;
        }

        $seedData = json_decode((string) file_get_contents($dataFile), true);
        $menuPages = $seedData['menu_pages'] ?? [];
        $allCategories = $seedData['categories'] ?? [];
        $bookableServices = $seedData['bookable_services'] ?? [];

        if (Schema::hasColumn('services', 'is_bookable')) {
            Service::query()->update(['is_bookable' => false]);
        }

        // 1. Seed 5 Hair Lengths
        $lengths = [
            'v_short' => ['ar' => 'قصير جداً', 'en' => 'V. SHORT', 'desc_ar' => 'حتى مستوى الأذن وأعلى الرقبة', 'cm' => 15, 'order' => 1],
            'short' => ['ar' => 'قصير', 'en' => 'SHORT', 'desc_ar' => 'مستوى الكتف أو أعلى قليلاً', 'cm' => 25, 'order' => 2],
            'medium' => ['ar' => 'وسط', 'en' => 'MEDIUM', 'desc_ar' => 'منتصف الظهر أو خط حمالة الصدر', 'cm' => 40, 'order' => 3],
            'long' => ['ar' => 'طويل', 'en' => 'LONG', 'desc_ar' => 'أسفل الظهر حتى خط الخصر', 'cm' => 55, 'order' => 4],
            'v_long' => ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'desc_ar' => 'يتجاوز الخصر نحو أسفل الظهر', 'cm' => 70, 'order' => 5],
        ];

        $lengthModels = [];
        foreach ($lengths as $code => $info) {
            $lengthModels[$code] = HairLength::updateOrCreate(
                ['code' => $code],
                [
                    'name' => [
                        'ar' => $info['ar'],
                        'en' => $info['en'],
                    ],
                    'description' => [
                        'ar' => $info['desc_ar'],
                        'en' => "Hair length up to {$info['cm']} cm approx.",
                    ],
                    'approx_length_cm' => $info['cm'],
                    'diagram_order' => $info['order'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Seed 8 Catalog Pages
        $pageModels = [];
        foreach ($menuPages as $page) {
            $pageNumber = $page['page_number'] ?? 1;
            $pageModels[$pageNumber] = CatalogPage::updateOrCreate(
                ['page_number' => $pageNumber],
                [
                    'title' => [
                        'ar' => $page['title_ar'] ?? 'خدمات الصالون',
                        'en' => $page['title_en'] ?? 'SALON SERVICES',
                    ],
                    'layout_type' => $page['layout_type'] ?? 'groups',
                    'meta' => $page,
                    'is_active' => true,
                ]
            );
        }

        // 3. Seed 7 Categories & Services
        $serviceCount = 0;
        foreach ($allCategories as $catIndex => $catData) {
            $pageRef = $catData['page_ref'] ?? 1;
            $catalogPage = $pageModels[$pageRef] ?? null;

            $categoryModel = ServiceCategory::updateOrCreate(
                ['slug' => $catData['id']],
                [
                    'catalog_page_id' => $catalogPage?->id,
                    'name' => [
                        'ar' => $catData['title'],
                        'en' => $catData['title_en'],
                    ],
                    'subtitle' => [
                        'ar' => $catData['subtitle'] ?? '',
                        'en' => 'Professional salon services and care.',
                    ],
                    'icon' => $catData['icon'] ?? 'scissors',
                    'page_ref' => $pageRef,
                    'sort_order' => $catIndex + 1,
                    'is_active' => true,
                ]
            );

            // Grouping services
            $groupedServices = [];
            foreach ($catData['services'] as $srv) {
                $grpName = $srv['group'] ?? 'الخدمات العامة';
                $groupedServices[$grpName][] = $srv;
            }

            $grpOrder = 0;
            foreach ($groupedServices as $groupName => $servicesList) {
                $grpOrder++;
                $groupModel = ServiceGroup::updateOrCreate(
                    [
                        'category_id' => $categoryModel->id,
                        'name->ar' => $groupName,
                    ],
                    [
                        'name' => [
                            'ar' => $groupName,
                            'en' => strtoupper(Str::slug($groupName, ' ')),
                        ],
                        'type' => 'side_label',
                        'container_class' => '',
                        'sort_order' => $grpOrder,
                        'is_active' => true,
                    ]
                );

                foreach ($servicesList as $srvIndex => $srv) {
                    $serviceCount++;
                    $srvCode = $srv['id'] ?? 'srv_'.Str::slug($srv['name']);

                    // Check if service has length tier
                    $hasLengthPricing = ! empty($srv['length_tier']);
                    $tierCode = $srv['length_tier'] ?? null;

                    $serviceModel = Service::updateOrCreate(
                        ['code' => $srvCode],
                        [
                            'category_id' => $categoryModel->id,
                            'group_id' => $groupModel->id,
                            'name' => [
                                'ar' => $srv['name'],
                                'en' => $srv['name_en'] ?? strtoupper(Str::slug($srv['name'], ' ')),
                            ],
                            'inclusions' => ! empty($srv['inclusions']) ? [
                                'ar' => $srv['inclusions'],
                                'en' => 'Includes full care session',
                            ] : null,
                            'description' => [
                                'ar' => $srv['description'] ?? '',
                                'en' => 'High quality treatment with certified premium products.',
                            ],
                            'price_display_override' => ! empty($srv['price_display']) ? [
                                'ar' => $srv['price_display'],
                                'en' => $srv['price_display'],
                            ] : null,
                            'base_price' => (float) ($srv['price'] ?? 0),
                            'duration_minutes' => (int) ($srv['duration_min'] ?? 60),
                            'buffer_minutes' => 10,
                            'has_length_pricing' => $hasLengthPricing,
                            'is_popular' => (bool) ($srv['is_popular'] ?? false),
                            'is_bookable' => (bool) ($srv['is_bookable'] ?? ($bookableServices[$srvCode]['is_bookable'] ?? false)),
                            'is_home_available' => (bool) ($srv['is_home_available'] ?? false),
                            'sort_order' => $srvIndex + 1,
                            'is_active' => true,
                        ]
                    );

                    // If length tier applies, insert into service_pricing_tiers
                    if ($hasLengthPricing && isset($lengthModels[$tierCode])) {
                        ServicePricingTier::updateOrCreate(
                            [
                                'service_id' => $serviceModel->id,
                                'hair_length_id' => $lengthModels[$tierCode]->id,
                            ],
                            [
                                'custom_label' => [
                                    'ar' => $srv['length_label'] ?? $lengthModels[$tierCode]->getTranslation('name', 'ar'),
                                    'en' => $lengthModels[$tierCode]->getTranslation('name', 'en'),
                                ],
                                'price' => (float) ($srv['price'] ?? 0),
                                'extra_duration_minutes' => 0,
                                'sort_order' => $lengthModels[$tierCode]->diagram_order,
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
        }

        // 4. Enrich multi-length blocks
        $categoriesBySlug = ServiceCategory::all()->keyBy('slug');
        $this->enrichMultiLengthBlocks($lengthModels, $categoriesBySlug, $menuPages);

        // 5. Sync explicit bookable_services and their specialists from catalog_seed_data.json
        $this->seedBookableServices($bookableServices, $categoriesBySlug);
    }

    /**
     * Ensure all entries in bookable_services exist in services table with is_bookable = true
     * and are linked to their designated specialists.
     *
     * @param  array<string, array<string, mixed>>  $bookableServices
     */
    private function seedBookableServices(array $bookableServices, $categories): void
    {
        $categorySlugMap = [
            'color_services' => 'hair_services',
            'hairstyles' => 'hair_services',
            'hot_treatments' => 'hair_treatments',
            'cold_treatments' => 'hair_treatments',
            'makeup' => 'skin_eyebrows',
        ];

        $groupNameMap = [
            'hs_full_color' => 'الصبغات',
            'hs_highlights' => 'الصبغات',
            'hs_all_hairstyles' => 'التساريح',
            'ht_protein_machine' => 'المعالجات الحارة',
            'ht_protein_no_machine' => 'المعالجات الحارة',
            'ht_roots_protein' => 'المعالجات الحارة',
            'ht_morphsis_filler' => 'فيلر المورفوسيس',
            'ht_trendy_session' => 'جلسة الترندي',
            'sk_makeup_lashes' => 'المكياج',
        ];

        $order = 100;
        foreach ($bookableServices as $srvCode => $bData) {
            $order++;
            $isBookable = (bool) ($bData['is_bookable'] ?? true);
            $catSlug = $categorySlugMap[$bData['category_id'] ?? ''] ?? 'hair_services';
            $category = $categories->get($catSlug) ?? ServiceCategory::first();

            if (! $category) {
                continue;
            }

            $groupName = $groupNameMap[$srvCode] ?? ($bData['category_title'] ?? 'الخدمات العامة');
            $group = ServiceGroup::where('category_id', $category->id)
                ->where('name->ar', $groupName)
                ->first();

            $durationMinutes = (int) preg_replace('/[^0-9]/', '', (string) ($bData['duration'] ?? '60')) ?: 60;
            $basePrice = (float) preg_replace('/[^0-9.]/', '', (string) ($bData['price'] ?? '0'));

            $serviceModel = Service::where('code', $srvCode)->first();
            if ($serviceModel) {
                $serviceModel->update([
                    'is_bookable' => $isBookable,
                    'is_active' => true,
                ]);
            } else {
                $serviceModel = Service::create([
                    'category_id' => $category->id,
                    'group_id' => $group?->id,
                    'code' => $srvCode,
                    'name' => [
                        'ar' => $bData['name'] ?? $srvCode,
                        'en' => $bData['category_title_en'] ?? strtoupper(Str::slug($srvCode, ' ')),
                    ],
                    'description' => [
                        'ar' => $bData['desc'] ?? '',
                        'en' => 'High quality treatment with certified premium products.',
                    ],
                    'price_display_override' => [
                        'ar' => $basePrice.' SR',
                        'en' => $basePrice.' SR',
                    ],
                    'base_price' => $basePrice,
                    'duration_minutes' => $durationMinutes,
                    'buffer_minutes' => 10,
                    'has_length_pricing' => false,
                    'is_popular' => false,
                    'is_bookable' => $isBookable,
                    'is_home_available' => false,
                    'sort_order' => $order,
                    'is_active' => true,
                ]);
            }

            $specialistSlugs = $bData['specialists'] ?? [];
            if (! empty($specialistSlugs)) {
                $specialistIds = Specialist::whereIn('slug', $specialistSlugs)->pluck('id');
                if ($specialistIds->isNotEmpty()) {
                    $syncData = [];
                    foreach ($specialistIds as $spId) {
                        $syncData[$spId] = ['custom_duration_minutes' => $serviceModel->duration_minutes];
                    }
                    $serviceModel->specialists()->sync($syncData);
                }
            }
        }
    }

    private function enrichMultiLengthBlocks(array $lengthModels, $categories, array $menuPages): void
    {
        foreach ($menuPages as $page) {
            $blocks = [];
            if (! empty($page['subsections'])) {
                foreach ($page['subsections'] as $sub) {
                    if (! empty($sub['blocks'])) {
                        $blocks = array_merge($blocks, $sub['blocks']);
                    }
                }
            }
            if (! empty($page['multi_length_blocks'])) {
                $blocks = array_merge($blocks, $page['multi_length_blocks']);
            }
            if (! empty($page['blocks'])) {
                $blocks = array_merge($blocks, $page['blocks']);
            }

            foreach ($blocks as $blk) {
                $arName = $blk['ar_name'] ?? null;
                $enName = $blk['en_name'] ?? '';
                if (! $arName) {
                    continue;
                }

                // Find matching service by name
                $matchingService = Service::where('name->ar', $arName)->first();
                if (! $matchingService) {
                    $cat = $categories->get('hair_treatments') ?? ServiceCategory::first();
                    $matchingService = Service::create([
                        'category_id' => $cat->id,
                        'code' => 'srv_'.Str::slug($enName ?: $arName, '_'),
                        'name' => ['ar' => $arName, 'en' => $enName ?: $arName],
                        'base_price' => 100,
                        'duration_minutes' => 60,
                        'has_length_pricing' => true,
                        'is_active' => true,
                    ]);
                } else {
                    $matchingService->update(['has_length_pricing' => true]);
                }

                if (! empty($blk['lengths'])) {
                    foreach ($blk['lengths'] as $idx => $lenItem) {
                        $lenAr = $lenItem['ar'] ?? '';
                        $lenPriceStr = $lenItem['price'] ?? '0';
                        $priceNum = (float) preg_replace('/[^0-9.]/', '', $lenPriceStr);

                        // Match length model
                        $hairLength = null;
                        foreach ($lengthModels as $lm) {
                            if ($lm->getTranslation('name', 'ar') === $lenAr || str_contains($lm->getTranslation('name', 'ar'), $lenAr)) {
                                $hairLength = $lm;
                                break;
                            }
                        }

                        if ($hairLength) {
                            ServicePricingTier::updateOrCreate(
                                [
                                    'service_id' => $matchingService->id,
                                    'hair_length_id' => $hairLength->id,
                                ],
                                [
                                    'custom_label' => ['ar' => $lenAr, 'en' => $lenItem['en'] ?? ''],
                                    'price' => $priceNum,
                                    'sort_order' => $idx + 1,
                                    'is_active' => true,
                                ]
                            );
                        }
                    }
                }
            }
        }
    }
}
