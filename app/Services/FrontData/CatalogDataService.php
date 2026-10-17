<?php

declare(strict_types=1);

namespace App\Services\FrontData;

/**
 * Provides static service catalog data for Hairline Beauty Lounge.
 *
 * Data is structured in categories, each containing sub-services with
 * pricing, duration, and description. Internal filtering and search
 * operate on the in-memory array — no database required.
 */
class CatalogDataService
{
    /**
     * @return array<int, array{
     *     id: string,
     *     title: string,
     *     subtitle: string,
     *     badge_count: string,
     *     icon: string,
     *     services: array<int, array{
     *         id: string,
     *         name: string,
     *         description: string,
     *         duration: string,
     *         duration_min: int,
     *         price: int,
     *         currency: string,
     *         is_popular: bool,
     *         is_home_available: bool
     *     }>
     * }>
     */
    public function getAllCategories(): array
    {
        return [
            [
                'id' => 'hair',
                'title' => 'خدمات الشعر',
                'subtitle' => 'قص، تصفيف، صبغات، ومعالجات ملكية',
                'badge_count' => '5 خدمات',
                'icon' => 'scissors',
                'services' => [
                    [
                        'id' => 'h1',
                        'name' => 'قص وتصفيف الشعر الاحترافي',
                        'description' => 'قص احترافي يناسب شكل وجهكِ مع تصفيف وسشوار فاخر',
                        'duration' => '45 دقيقة',
                        'duration_min' => 45,
                        'price' => 180,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => true,
                    ],
                    [
                        'id' => 'h2',
                        'name' => 'سشوار وويفي هيرلاين الفاخر',
                        'description' => 'سشوار ناعم أو ويفي مموج بتقنية البروتين الحراري',
                        'duration' => '45 دقيقة',
                        'duration_min' => 45,
                        'price' => 160,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => true,
                    ],
                    [
                        'id' => 'h3',
                        'name' => 'صبغة جذور ولون كامل باللمعة الحريرية',
                        'description' => 'تغطية شاملة للجذور والطول مع معالجة اللمعة الحريرية',
                        'duration' => '120 دقيقة',
                        'duration_min' => 120,
                        'price' => 480,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 'h4',
                        'name' => 'معالجات الكافيار والبروتين البرازيلي',
                        'description' => 'معالجة كيراتين برازيلية أو كافيار فاخر لترميم الشعر وتنعيمه',
                        'duration' => '150 دقيقة',
                        'duration_min' => 150,
                        'price' => 650,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 'h5',
                        'name' => 'تسريحة مناسبات وسهرة ملكية',
                        'description' => 'تسريحة رفيعة مميزة للمناسبات الخاصة والسهرات',
                        'duration' => '75 دقيقة',
                        'duration_min' => 75,
                        'price' => 350,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => true,
                    ],
                ],
            ],
            [
                'id' => 'nails',
                'title' => 'قسم الأظافر',
                'subtitle' => 'مانيكير روسي، باديكير سبا، وجل بولش',
                'badge_count' => '4 خدمات',
                'icon' => 'hand',
                'services' => [
                    [
                        'id' => 'n1',
                        'name' => 'مانيكير روسي دقيق وتنظيف محيط الظفر',
                        'description' => 'مانيكير روسي احترافي مع تشكيل الأظافر وترطيب الجلد المحيط',
                        'duration' => '50 دقيقة',
                        'duration_min' => 50,
                        'price' => 150,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => true,
                    ],
                    [
                        'id' => 'n2',
                        'name' => 'باديكير سبا علاجي بالأملاح والزيوت',
                        'description' => 'باديكير علاجي شامل بمنتجات الأملاح البحرية وزيوت الأعشاب',
                        'duration' => '60 دقيقة',
                        'duration_min' => 60,
                        'price' => 190,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => true,
                    ],
                    [
                        'id' => 'n3',
                        'name' => 'جل بولش وتقوية الأظافر (BIAB)',
                        'description' => 'طلاء جل طويل الأمد مع BIAB لتقوية الأظافر الهشة',
                        'duration' => '60 دقيقة',
                        'duration_min' => 60,
                        'price' => 220,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 'n4',
                        'name' => 'تركيب وتصميم أظافر أكريليك / جل إكستنشن',
                        'description' => 'تركيب أظافر أكريليك أو جل بأشكال وتصاميم مخصصة',
                        'duration' => '90 دقيقة',
                        'duration_min' => 90,
                        'price' => 340,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                ],
            ],
            [
                'id' => 'skin_makeup',
                'title' => 'العناية بالبشرة والمكياج',
                'subtitle' => 'جلسات نضارة ومكياج سهرة ونهاري',
                'badge_count' => '5 خدمات',
                'icon' => 'face',
                'services' => [
                    [
                        'id' => 's1',
                        'name' => 'تنظيف بشرة عميق (هيدرافيشل الملكي)',
                        'description' => 'تنظيف عميق للمسام مع رفع الشوائب والترطيب الفوري',
                        'duration' => '60 دقيقة',
                        'duration_min' => 60,
                        'price' => 380,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 's2',
                        'name' => 'جلسة نضارة الكولاجين والذهب عيار 24',
                        'description' => 'جلسة ترطيب ومضادات أكسدة بالكولاجين والذهب الخالص',
                        'duration' => '75 دقيقة',
                        'duration_min' => 75,
                        'price' => 460,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 's3',
                        'name' => 'مكياج سهرة فاخر مع رموش طبيعية',
                        'description' => 'مكياج سهرة كامل بمنتجات Dior وNARS مع رموش طبيعية',
                        'duration' => '75 دقيقة',
                        'duration_min' => 75,
                        'price' => 450,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => true,
                    ],
                    [
                        'id' => 's4',
                        'name' => 'مكياج ناعم (Soft Glam)',
                        'description' => 'مكياج نهاري ناعم وأنيق يناسب الإطلالات العصرية',
                        'duration' => '50 دقيقة',
                        'duration_min' => 50,
                        'price' => 320,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => true,
                    ],
                    [
                        'id' => 's5',
                        'name' => 'رفع وتوريد الرموش وتشقير الحواجب',
                        'description' => 'رفع الرموش بتقنية اللامينيشن مع تشكيل الحواجب بالشعيرات',
                        'duration' => '45 دقيقة',
                        'duration_min' => 45,
                        'price' => 240,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                ],
            ],
            [
                'id' => 'hammam_spa',
                'title' => 'الحمام المغربي والسبا',
                'subtitle' => 'طقوس استرخاء ملكية وعناية فائقة بالجسم',
                'badge_count' => '4 خدمات',
                'icon' => 'spa',
                'services' => [
                    [
                        'id' => 'm1',
                        'name' => 'الحمام المغربي الملكي بالأعشاب الطبيعية',
                        'description' => 'حمام بخار كامل مع تقشير الصابون البلدي والأعشاب الطبيعية',
                        'duration' => '75 دقيقة',
                        'duration_min' => 75,
                        'price' => 350,
                        'currency' => 'SAR',
                        'is_popular' => true,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 'm2',
                        'name' => 'حمام العروس الفاخر بالورد والعود',
                        'description' => 'باقة شاملة للعروس بالورد والعود الأصيل وزيوت الصندل',
                        'duration' => '110 دقيقة',
                        'duration_min' => 110,
                        'price' => 580,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 'm3',
                        'name' => 'مساج استرخائي بالزيوت العطرية الدافئة',
                        'description' => 'مساج جسم كامل بالزيوت العطرية لإزالة التعب والتوتر',
                        'duration' => '60 دقيقة',
                        'duration_min' => 60,
                        'price' => 290,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                    [
                        'id' => 'm4',
                        'name' => 'جلسة ديتوكس وتقشير الجسم بالقهوة والذهب',
                        'description' => 'تقشير فاخر بالقهوة العربية ومستخلص الذهب لبشرة ناعمة متألقة',
                        'duration' => '60 دقيقة',
                        'duration_min' => 60,
                        'price' => 320,
                        'currency' => 'SAR',
                        'is_popular' => false,
                        'is_home_available' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Find a single service by its ID across all categories.
     *
     * @return array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     duration: string,
     *     duration_min: int,
     *     price: int,
     *     currency: string,
     *     is_popular: bool,
     *     is_home_available: bool,
     *     category_id: string,
     *     category_title: string
     * }|null
     */
    public function findServiceById(string $serviceId): ?array
    {
        foreach ($this->getAllCategories() as $category) {
            foreach ($category['services'] as $service) {
                if ($service['id'] === $serviceId) {
                    return array_merge($service, [
                        'category_id' => $category['id'],
                        'category_title' => $category['title'],
                    ]);
                }
            }
        }

        return null;
    }

    /**
     * Search services by name or description keyword (case-insensitive, Arabic-aware).
     *
     * @return array<int, array{service: array<string, mixed>, category_id: string, category_title: string}>
     */
    public function searchServices(string $query): array
    {
        if (empty(trim($query))) {
            return [];
        }

        $results = [];
        $queryLower = mb_strtolower($query);

        foreach ($this->getAllCategories() as $category) {
            foreach ($category['services'] as $service) {
                $nameMatch = str_contains(mb_strtolower($service['name']), $queryLower);
                $descMatch = str_contains(mb_strtolower($service['description']), $queryLower);

                if ($nameMatch || $descMatch) {
                    $results[] = [
                        'service' => $service,
                        'category_id' => $category['id'],
                        'category_title' => $category['title'],
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Filter services by category, price range, and availability.
     *
     * @param  array{
     *     category_id?: string|null,
     *     min_price?: int|null,
     *     max_price?: int|null,
     *     home_available?: bool|null,
     *     popular_only?: bool
     * } $filters
     * @return array<int, array{service: array<string, mixed>, category_id: string, category_title: string}>
     */
    public function filterServices(array $filters = []): array
    {
        $results = [];

        foreach ($this->getAllCategories() as $category) {
            if (! empty($filters['category_id']) && $category['id'] !== $filters['category_id']) {
                continue;
            }

            foreach ($category['services'] as $service) {
                if (isset($filters['min_price']) && $service['price'] < $filters['min_price']) {
                    continue;
                }
                if (isset($filters['max_price']) && $service['price'] > $filters['max_price']) {
                    continue;
                }
                if (isset($filters['home_available']) && $filters['home_available'] === true && ! $service['is_home_available']) {
                    continue;
                }
                if (! empty($filters['popular_only']) && ! $service['is_popular']) {
                    continue;
                }

                $results[] = [
                    'service' => $service,
                    'category_id' => $category['id'],
                    'category_title' => $category['title'],
                ];
            }
        }

        return $results;
    }

    /**
     * Sort a flat list of service results by a given field.
     *
     * @param  array<int, array{service: array<string, mixed>, category_id: string, category_title: string}>  $services
     * @param  'price'|'duration_min'|'name'  $sortBy
     * @param  'asc'|'desc'  $direction
     * @return array<int, array{service: array<string, mixed>, category_id: string, category_title: string}>
     */
    public function sortServices(array $services, string $sortBy = 'price', string $direction = 'asc'): array
    {
        usort($services, function (array $a, array $b) use ($sortBy, $direction): int {
            $valueA = $a['service'][$sortBy] ?? 0;
            $valueB = $b['service'][$sortBy] ?? 0;

            if (is_string($valueA) && is_string($valueB)) {
                $comparison = strcmp($valueA, $valueB);
            } else {
                $comparison = $valueA <=> $valueB;
            }

            return $direction === 'desc' ? -$comparison : $comparison;
        });

        return $services;
    }

    /**
     * Calculate totals for a list of service IDs.
     *
     * @param  string[]  $serviceIds
     * @return array{total_price: int, total_duration_min: int, total_duration_label: string, count: int}
     */
    public function calculateTotals(array $serviceIds): array
    {
        $totalPrice = 0;
        $totalDuration = 0;

        foreach ($serviceIds as $id) {
            $service = $this->findServiceById($id);
            if ($service !== null) {
                $totalPrice += $service['price'];
                $totalDuration += $service['duration_min'];
            }
        }

        $hours = intdiv($totalDuration, 60);
        $minutes = $totalDuration % 60;

        $durationLabel = $hours > 0
            ? "{$hours} ساعة".($minutes > 0 ? " و{$minutes} دقيقة" : '')
            : "{$totalDuration} دقيقة";

        return [
            'total_price' => $totalPrice,
            'total_duration_min' => $totalDuration,
            'total_duration_label' => $durationLabel,
            'count' => count($serviceIds),
        ];
    }
}
