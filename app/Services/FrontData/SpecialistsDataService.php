<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\Specialist;
use App\Services\Cache\HairlineCacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Provides database-backed and cached specialist data for Hairline Beauty Lounge.
 */
class SpecialistsDataService
{
    /**
     * Get the count of active specialists, cached.
     */
    public function getActiveSpecialistsCount(): int
    {
        return (int) Cache::rememberForever(HairlineCacheService::KEY_SPECIALISTS_COUNT, function (): int {
            return Specialist::where('is_active', true)->count();
        });
    }

    /**
     * Get the formatted display string for specialists count (e.g. "+5").
     */
    public function getFormattedSpecialistsCount(): string
    {
        $count = $this->getActiveSpecialistsCount();

        return $count > 0 ? "+{$count}" : '0';
    }

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
            $specialists = Specialist::with('shifts')->where('is_active', true)->orderBy('sort_order')->get();

            $result = [];
            foreach ($specialists as $sp) {
                $nameAr = $sp->getTranslation('name', 'ar');
                $nameEn = $sp->getTranslation('name', 'en');
                $fullName = $nameEn ? "{$nameAr} | {$nameEn}" : $nameAr;

                $slots = [];
                $firstShift = $sp->shifts->first();
                if ($firstShift) {
                    $start = Carbon::parse($firstShift->start_time);
                    $end = Carbon::parse($firstShift->end_time);
                    while ($start->copy()->addMinutes(60)->lte($end)) {
                        $slots[] = $start->format('h:i').($start->format('A') === 'AM' ? ' ص' : ' م');
                        $start->addHours(2)->addMinutes(30);
                    }
                }

                if (empty($slots)) {
                    $slots = ['11:30 ص', '02:00 م', '05:30 م', '08:00 م'];
                }

                $result[$sp->slug] = [
                    'id' => $sp->slug,
                    'name' => $fullName,
                    'role' => $sp->getTranslation('role', 'ar'),
                    'experience' => $sp->getTranslation('experience', 'ar'),
                    'avatar' => $sp->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($nameAr).'&background=211C18&color=C29E6D&bold=true&size=150',
                    'bio' => $sp->getTranslation('bio', 'ar') ?: '',
                    'availableSlots' => $slots,
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
