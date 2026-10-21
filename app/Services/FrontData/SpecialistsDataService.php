<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\Specialist;
use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Provides database-backed and cached specialist data for Hairline Beauty Lounge.
 */
class SpecialistsDataService
{
    /**
     * @return array<string, array{
     *     id: string,
     *     name: string,
     *     role: string,
     *     experience: string,
     *     avatar: string,
     *     bio: string,
     *     availableSlots: array<int, string>
     * }>
     */
    public function getAllSpecialists(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_SPECIALISTS_ALL, function () {
            $specialists = Specialist::where('is_active', true)->orderBy('sort_order')->get();

            $slotsMap = [
                'sp_karen' => ['11:30 ص', '02:00 م', '05:30 م', '08:00 م'],
                'sp_janet' => ['12:00 م', '03:30 م', '06:30 م', '08:30 م'],
                'sp_jenky' => ['01:00 م', '04:00 م', '07:00 م', '09:00 م'],
                'sp_ruth' => ['11:00 ص', '02:30 م', '06:00 م', '08:30 م'],
                'sp_najla' => ['12:30 م', '03:00 م', '05:30 م', '08:00 م'],
            ];

            $result = [];
            foreach ($specialists as $sp) {
                $nameAr = $sp->getTranslation('name', 'ar');
                $nameEn = $sp->getTranslation('name', 'en');
                $fullName = $nameEn ? "{$nameAr} | {$nameEn}" : $nameAr;

                $result[$sp->slug] = [
                    'id' => $sp->slug,
                    'name' => $fullName,
                    'role' => $sp->getTranslation('role', 'ar'),
                    'experience' => $sp->getTranslation('experience', 'ar'),
                    'avatar' => $sp->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($nameAr).'&background=211C18&color=C29E6D&bold=true&size=150',
                    'bio' => $sp->getTranslation('bio', 'ar') ?: '',
                    'availableSlots' => $slotsMap[$sp->slug] ?? ['02:00 م', '04:30 م', '07:00 م', '09:00 م'],
                ];
            }

            return $result;
        });
    }

    /**
     * @return ?array{
     *     id: string,
     *     name: string,
     *     role: string,
     *     experience: string,
     *     avatar: string,
     *     bio: string,
     *     availableSlots: array<int, string>
     * }
     */
    public function getSpecialistById(string $id): ?array
    {
        return $this->getAllSpecialists()[$id] ?? null;
    }
}
