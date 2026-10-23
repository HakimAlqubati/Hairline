<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\Specialist;
use App\Services\Booking\SpecialistAvailabilityService;

/**
 * Database-backed booking calendar data: specialists, available dates,
 * time slots, and interval-based conflict prevention logic.
 * Delegates core scheduling and conflict checks to SpecialistAvailabilityService.
 */
class BookingCalendarDataService
{
    public function __construct(
        private readonly SpecialistAvailabilityService $availabilityService
    ) {}

    /**
     * @return array<int, array{id: string, name: string, title: string, avatar_initials: string, speciality: string, is_home_specialist: bool}>
     */
    public function getSpecialists(): array
    {
        $specialists = Specialist::where('is_active', true)->orderBy('sort_order')->get();

        if ($specialists->isEmpty()) {
            return [
                [
                    'id' => 'sp1',
                    'name' => 'خبيرة سارة',
                    'title' => 'Senior Hair Stylist',
                    'avatar_initials' => 'سا',
                    'speciality' => 'خبيرات الشعر',
                    'is_home_specialist' => false,
                ],
            ];
        }

        return $specialists->map(function (Specialist $sp): array {
            $nameAr = $sp->getTranslation('name', 'ar');
            $initials = mb_substr($nameAr, 0, 2);

            return [
                'id' => $sp->slug,
                'name' => 'خبيرة '.$nameAr,
                'title' => $sp->getTranslation('role', 'ar') ?: 'Specialist',
                'avatar_initials' => $initials,
                'speciality' => $sp->getTranslation('role', 'ar'),
                'is_home_specialist' => (bool) $sp->is_home_specialist,
            ];
        })->all();
    }

    /**
     * Generate available dates starting from today (next 7 days).
     *
     * @return array<int, array{id: string, day_name: string, date_label: string, display_label: string, date_iso: string, day_of_week: int, is_today: bool, is_weekend: bool}>
     */
    public function getAvailableDates(): array
    {
        return $this->availabilityService->getUpcomingDates(7);
    }

    /**
     * Get available (non-conflicting) time slots for a given specialist and date from the database shift & bookings.
     *
     * @param  'salon'|'home'  $bookingType
     * @return array<int, array{time: string, time_24?: string, end_time_24?: string, is_available: bool, is_booked: bool}>
     */
    public function getAvailableSlots(string $specialistId, string $dateId, string $bookingType = 'salon'): array
    {
        $date = $this->availabilityService->resolveBookingDate($dateId);
        $specialist = $this->availabilityService->resolveSpecialist($specialistId)
            ?? Specialist::where('is_active', true)->orderBy('sort_order')->first();

        if (! $specialist) {
            return [];
        }

        $availability = $this->availabilityService->getAvailabilityForSpecialistDate(
            specialist: $specialist,
            date: $date,
            durationMinutes: 60
        );

        return $availability['slots'];
    }

    /**
     * Check if a specific slot is still available (shift + conflict prevention).
     */
    public function isSlotAvailable(string $specialistId, string $dateId, string $time, string $bookingType = 'salon'): bool
    {
        foreach ($this->getAvailableSlots($specialistId, $dateId, $bookingType) as $slot) {
            if ($slot['time'] === $time) {
                return $slot['is_available'];
            }
        }

        return false;
    }

    /**
     * Find the first available slot for a date.
     */
    public function getFirstAvailableSlot(string $specialistId, string $dateId, string $bookingType = 'salon'): ?string
    {
        foreach ($this->getAvailableSlots($specialistId, $dateId, $bookingType) as $slot) {
            if ($slot['is_available']) {
                return $slot['time'];
            }
        }

        return null;
    }
}
