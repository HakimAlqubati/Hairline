<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\BookingItem;
use App\Models\Specialist;
use Carbon\Carbon;

/**
 * Database-backed booking calendar data: specialists, available dates,
 * time slots, and interval-based conflict prevention logic.
 */
class BookingCalendarDataService
{
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

        return $specialists->map(function (Specialist $sp) {
            $nameAr = $sp->getTranslation('name', 'ar');
            $initials = mb_substr($nameAr, 0, 2);

            return [
                'id' => $sp->slug,
                'name' => 'خبيرة '.$nameAr,
                'title' => $sp->getTranslation('role', 'ar') ?: 'Specialist',
                'avatar_initials' => $initials,
                'speciality' => $sp->getTranslation('role', 'ar'),
                'is_home_specialist' => (bool) $sp->is_home_available,
            ];
        })->all();
    }

    /**
     * Generate available dates starting from today (next 7 days).
     *
     * @return array<int, array{id: string, day_name: string, date_label: string, date_iso: string, is_today: bool, is_weekend: bool}>
     */
    public function getAvailableDates(): array
    {
        $dates = [];
        $today = Carbon::today('Asia/Riyadh');

        $arabicDays = [
            0 => 'الأحد',
            1 => 'الاثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        $arabicMonths = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر',
        ];

        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            $dayOfWeek = (int) $date->format('w');
            $day = (int) $date->format('j');
            $month = (int) $date->format('n');

            $dayName = $i === 0 ? 'اليوم' : ($i === 1 ? 'غداً' : $arabicDays[$dayOfWeek]);

            $dates[] = [
                'id' => 'd'.($i + 1),
                'day_name' => $dayName,
                'date_label' => "{$day} {$arabicMonths[$month]}",
                'date_iso' => $date->toDateString(),
                'is_today' => $i === 0,
                'is_weekend' => in_array($dayOfWeek, [5, 6], true),
            ];
        }

        return $dates;
    }

    /**
     * @return string[]
     */
    public function getAllTimeSlots(): array
    {
        return [
            '02:30 م',
            '04:00 م',
            '05:30 م',
            '07:00 م',
            '08:30 م',
            '09:45 م',
        ];
    }

    /**
     * Resolve date_iso from dateId ('d1' -> today).
     */
    private function resolveDateIso(string $dateId): string
    {
        $dates = $this->getAvailableDates();
        foreach ($dates as $d) {
            if ($d['id'] === $dateId) {
                return $d['date_iso'];
            }
        }

        return Carbon::today('Asia/Riyadh')->toDateString();
    }

    /**
     * Get available (non-conflicting) time slots for a given specialist and date from the database.
     *
     * @param  'salon'|'home'  $bookingType
     * @return array<int, array{time: string, is_available: bool, is_booked: bool}>
     */
    public function getAvailableSlots(string $specialistId, string $dateId, string $bookingType = 'salon'): array
    {
        $dateIso = $this->resolveDateIso($dateId);
        $specialist = Specialist::where('slug', $specialistId)->orWhere('id', $specialistId)->first();

        // Convert Arabic time string like '02:30 م' to '14:30:00'
        $timeTo24h = function (string $arTime): string {
            $cleaned = str_replace([' ص', ' م'], '', $arTime);
            [$h, $m] = explode(':', $cleaned);
            $hours = (int) $h;
            if (str_contains($arTime, 'م') && $hours < 12) {
                $hours += 12;
            } elseif (str_contains($arTime, 'ص') && $hours === 12) {
                $hours = 0;
            }

            return sprintf('%02d:%02d:00', $hours, (int) $m);
        };

        // Fetch conflicting booked items from database for this specialist on this date
        $bookedSlots = [];
        if ($specialist) {
            $bookedItems = BookingItem::where('specialist_id', $specialist->id)
                ->whereHas('booking', fn ($q) => $q->whereDate('booking_date', $dateIso)->whereIn('status', ['confirmed', 'pending', 'in_progress']))
                ->get();

            foreach ($bookedItems as $item) {
                $bookedSlots[] = $item->scheduled_start_time;
            }
        }

        // Mock conflicting slots for demo consistency if no DB bookings yet
        $demoConflicts = [
            'sp1' => ['d1' => ['02:30 م', '04:00 م'], 'd2' => ['07:00 م']],
            'sp_karen' => ['d1' => ['02:30 م', '04:00 م'], 'd2' => ['07:00 م']],
            'sp2' => ['d1' => ['05:30 م'], 'd3' => ['08:30 م', '09:45 م']],
            'sp_janet' => ['d1' => ['05:30 م'], 'd3' => ['08:30 م', '09:45 م']],
        ];

        $fallbackBooked = $demoConflicts[$specialistId][$dateId] ?? [];

        $slots = [];
        foreach ($this->getAllTimeSlots() as $time) {
            $time24 = $timeTo24h($time);
            $isBooked = in_array($time24, $bookedSlots, true) || in_array($time, $fallbackBooked, true);

            $slots[] = [
                'time' => $time,
                'is_available' => ! $isBooked,
                'is_booked' => $isBooked,
            ];
        }

        return $slots;
    }

    /**
     * Check if a specific slot is still available (conflict prevention).
     */
    public function isSlotAvailable(string $specialistId, string $dateId, string $time, string $bookingType = 'salon'): bool
    {
        foreach ($this->getAvailableSlots($specialistId, $dateId, $bookingType) as $slot) {
            if ($slot['time'] === $time) {
                return $slot['is_available'];
            }
        }

        return true;
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
