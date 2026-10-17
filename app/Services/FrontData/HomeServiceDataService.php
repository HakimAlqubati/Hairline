<?php

declare(strict_types=1);

namespace App\Services\FrontData;

/**
 * Provides static data for the home-service offering.
 *
 * Manages covered districts (12 neighbourhoods in north, east, and west Riyadh),
 * pricing, and availability checking — all from in-memory arrays.
 */
class HomeServiceDataService
{
    /**
     * @return array{
     *     is_available: bool,
     *     title: string,
     *     description: string,
     *     base_fee: int,
     *     base_fee_label: string,
     *     currency: string,
     *     min_order_amount: int,
     *     booking_notice_hours: int,
     *     cancellation_hours: int,
     *     notes: string[]
     * }
     */
    public function getHomeServiceDetails(): array
    {
        return [
            'is_available' => true,
            'title' => 'خدمة الصالون في بيتكِ',
            'description' => 'نحضر إليكِ أينما كنتِ في الرياض. فريق خبيراتنا المتنقل يصلكِ في وقتكِ المحدد.',
            'base_fee' => 50,
            'base_fee_label' => 'رسوم التنقل والزيارة',
            'currency' => 'SAR',
            'min_order_amount' => 200,
            'booking_notice_hours' => 24,
            'cancellation_hours' => 12,
            'notes' => [
                'يجب توفير مكان مناسب ومضاء للخدمة',
                'رسوم الزيارة 50 ريال تُضاف لقيمة الخدمات',
                'الحد الأدنى للطلب 200 ريال (بدون رسوم الزيارة)',
                'يُرجى الحجز قبل 24 ساعة على الأقل',
                'الإلغاء المجاني قبل 12 ساعة من الموعد',
            ],
        ];
    }

    /**
     * Returns the 12 covered districts grouped by geographic zone.
     *
     * @return array<string, array<int, array{
     *     id: string,
     *     name: string,
     *     zone: string,
     *     is_available: bool,
     *     extra_fee: int
     * }>>
     */
    public function getCoveredDistrictsGrouped(): array
    {
        return [
            'شمال الرياض' => [
                ['id' => 'd_malqa',    'name' => 'حي الملقا',     'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_nakheel',  'name' => 'حي النخيل',     'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_yasmin',   'name' => 'حي الياسمين',   'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_qurtuba', 'name' => 'حي قرطبة',      'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
            ],
            'شرق الرياض' => [
                ['id' => 'd_rawdah',   'name' => 'حي الروضة',     'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_zahra',    'name' => 'حي الزهراء',    'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_mansura',  'name' => 'حي المنصورة',   'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 15],
                ['id' => 'd_khaleej',  'name' => 'حي الخليج',     'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 15],
            ],
            'غرب الرياض' => [
                ['id' => 'd_sulayman', 'name' => 'حي السلطان',    'zone' => 'غرب الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_izdihar',  'name' => 'حي الإزدهار',   'zone' => 'غرب الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_rabwa',    'name' => 'حي الربوة',     'zone' => 'غرب الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_hittin',   'name' => 'حي حطين',       'zone' => 'غرب الرياض',  'is_available' => false, 'extra_fee' => 0],
            ],
        ];
    }

    /**
     * Returns a flat list of all 12 covered districts.
     *
     * @return array<int, array{id: string, name: string, zone: string, is_available: bool, extra_fee: int}>
     */
    public function getAllDistricts(): array
    {
        $flat = [];
        foreach ($this->getCoveredDistrictsGrouped() as $districts) {
            foreach ($districts as $district) {
                $flat[] = $district;
            }
        }

        return $flat;
    }

    /**
     * Check whether the home service is available in a given district.
     *
     * @return array{
     *     is_covered: bool,
     *     district: array{id: string, name: string, zone: string, is_available: bool, extra_fee: int}|null,
     *     message: string
     * }
     */
    public function checkDistrictAvailability(string $districtId): array
    {
        foreach ($this->getAllDistricts() as $district) {
            if ($district['id'] === $districtId) {
                if ($district['is_available']) {
                    $message = $district['extra_fee'] > 0
                        ? "الخدمة المنزلية متوفرة في {$district['name']} مع رسوم إضافية {$district['extra_fee']} ريال."
                        : "الخدمة المنزلية متوفرة في {$district['name']} بدون رسوم إضافية.";

                    return [
                        'is_covered' => true,
                        'district' => $district,
                        'message' => $message,
                    ];
                }

                return [
                    'is_covered' => false,
                    'district' => $district,
                    'message' => "نأسف، الخدمة المنزلية غير متوفرة حالياً في {$district['name']}. يسعدنا خدمتكِ في الصالون.",
                ];
            }
        }

        return [
            'is_covered' => false,
            'district' => null,
            'message' => 'الحي المختار غير مدرج ضمن نطاق خدمتنا المنزلية حالياً.',
        ];
    }

    /**
     * Calculate total cost including home service fee for a given district.
     *
     * @param  string[]  $serviceIds
     * @return array{
     *     services_total: int,
     *     visit_fee: int,
     *     extra_district_fee: int,
     *     grand_total: int,
     *     currency: string
     * }
     */
    public function calculateHomeServiceTotal(array $serviceIds, string $districtId, CatalogDataService $catalog): array
    {
        $totals = $catalog->calculateTotals($serviceIds);
        $availability = $this->checkDistrictAvailability($districtId);
        $details = $this->getHomeServiceDetails();

        $extraFee = ($availability['district']['extra_fee'] ?? 0);
        $visitFee = $details['base_fee'];
        $grandTotal = $totals['total_price'] + $visitFee + $extraFee;

        return [
            'services_total' => $totals['total_price'],
            'visit_fee' => $visitFee,
            'extra_district_fee' => $extraFee,
            'grand_total' => $grandTotal,
            'currency' => 'SAR',
        ];
    }
}
