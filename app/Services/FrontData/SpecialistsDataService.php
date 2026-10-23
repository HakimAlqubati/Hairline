<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\Specialist;
use App\Services\Booking\SpecialistAvailabilityService;
use App\Services\Cache\HairlineCacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Provides database-backed and cached specialist data for Hairline Beauty Lounge.
 */
class SpecialistsDataService
{
    public function __construct(
        private readonly SpecialistAvailabilityService $availabilityService
    ) {}

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
        return Cache::rememberForever(HairlineCacheService::KEY_SPECIALISTS_ALL, function (): array {
            $specialists = Specialist::with('shifts')->where('is_active', true)->orderBy('sort_order')->get();
            $defaultDate = Carbon::today('Asia/Riyadh')->addDay();

            $result = [];
            foreach ($specialists as $sp) {
                $nameAr = $sp->getTranslation('name', 'ar');
                $nameEn = $sp->getTranslation('name', 'en');
                $fullName = $nameEn ? "{$nameAr} | {$nameEn}" : $nameAr;

                $shiftForDay = $sp->shifts->firstWhere('day_of_week', (int) $defaultDate->dayOfWeek)
                    ?? $sp->shifts->firstWhere('is_day_off', false);

                $availability = $this->availabilityService->getAvailabilityForSpecialistDate(
                    specialist: $sp,
                    date: $defaultDate,
                    durationMinutes: 60,
                    preloadedShift: $shiftForDay,
                    preloadedIntervals: []
                );

                $slots = $availability['available_times'];
                if (empty($slots)) {
                    $slots = ['02:00 م', '03:30 م', '05:00 م', '06:30 م', '08:00 م'];
                }

                $avatar = $sp->avatar_url;
                if ($avatar && ! str_starts_with($avatar, 'http') && ! str_starts_with($avatar, 'data:')) {
                    $avatar = (str_starts_with($avatar, 'images/') || str_starts_with($avatar, '/images/'))
                        ? asset(ltrim($avatar, '/'))
                        : asset('storage/'.ltrim($avatar, '/'));
                }

                $result[$sp->slug] = [
                    'id' => $sp->slug,
                    'name' => $fullName,
                    'role' => $sp->getTranslation('role', 'ar'),
                    'experience' => $sp->getTranslation('experience', 'ar'),
                    'avatar' => $avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($nameAr).'&background=211C18&color=C29E6D&bold=true&size=150',
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
