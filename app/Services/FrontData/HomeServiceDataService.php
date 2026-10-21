<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\CancellationPolicy;
use App\Models\District;
use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Provides database-backed and cached data for the home-service offering.
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
        $policy = CancellationPolicy::where('is_active', true)->first();
        $freeHours = $policy?->free_cancellation_hours ?? 12;

        return [
            'is_available' => true,
            'title' => 'خدمة الصالون في بيتكِ',
            'description' => 'نحضر إليكِ أينما كنتِ في الرياض. فريق خبيراتنا المتنقل يصلكِ في وقتكِ المحدد.',
            'base_fee' => 50,
            'base_fee_label' => 'رسوم التنقل والزيارة',
            'currency' => 'SAR',
            'min_order_amount' => 200,
            'booking_notice_hours' => 24,
            'cancellation_hours' => $freeHours,
            'notes' => [
                'يجب توفير مكان مناسب ومضاء للخدمة',
                'رسوم الزيارة 50 ريال تُضاف لقيمة الخدمات',
                'الحد الأدنى للطلب 200 ريال (بدون رسوم الزيارة)',
                'يُرجى الحجز قبل 24 ساعة على الأقل',
                "الإلغاء المجاني قبل {$freeHours} ساعة من الموعد",
            ],
        ];
    }

    /**
     * Returns the covered districts grouped by geographic zone from database.
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
        return Cache::rememberForever(HairlineCacheService::KEY_DISTRICTS, function () {
            $districts = District::all();

            $grouped = [];
            foreach ($districts as $d) {
                $zone = $d->getTranslation('zone', 'ar') ?: 'شمال الرياض';
                $grouped[$zone][] = [
                    'id' => $d->slug,
                    'name' => $d->getTranslation('name', 'ar'),
                    'zone' => $zone,
                    'is_available' => (bool) $d->is_available,
                    'extra_fee' => (int) $d->extra_fee,
                ];
            }

            return $grouped;
        });
    }

    /**
     * Returns a flat list of all covered districts.
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
                    'message' => "عذراً، الخدمة المنزلية غير متوفرة حالياً في {$district['name']}.",
                ];
            }
        }

        return [
            'is_covered' => false,
            'district' => null,
            'message' => 'عذراً، هذا الحي غير مشمول في مناطق التغطية حالياً.',
        ];
    }
}
