<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use Carbon\Carbon;

/**
 * Provides static booking calendar data: specialists, available dates,
 * time slots, and simulated conflict-prevention logic.
 *
 * Booking types:
 *  - 'salon'  : In-salon appointments (الصالون الداخلي)
 *  - 'home'   : Home-service appointments (الخدمة المنزلية)
 */
class BookingCalendarDataService
{
    /**
     * Pre-filled "booked" slots to simulate conflict prevention.
     * Format: ['specialist_id' => ['date_id' => ['time_slot', ...]], ...]
     *
     * @var array<string, array<string, string[]>>
     */
    private const BOOKED_SLOTS_SALON = [
        'sp1' => [
            'd1' => ['02:30 م', '04:00 م'],
            'd2' => ['07:00 م'],
        ],
        'sp2' => [
            'd1' => ['05:30 م'],
            'd3' => ['08:30 م', '09:45 م'],
        ],
    ];

    /**
     * @var array<string, array<string, string[]>>
     */
    private const BOOKED_SLOTS_HOME = [
        'sp3' => [
            'd1' => ['04:00 م', '07:00 م'],
        ],
    ];

    /**
     * @return array<int, array{id: string, name: string, title: string, avatar_initials: string, speciality: string, is_home_specialist: bool}>
     */
    public function getSpecialists(): array
    {
        return [
            [
                'id' => 'sp1',
                'name' => 'خبيرة سارة',
                'title' => 'Senior Hair Stylist',
                'avatar_initials' => 'سا',
                'speciality' => 'خبيرات الشعر',
                'is_home_specialist' => false,
            ],
            [
                'id' => 'sp2',
                'name' => 'خبيرة نورة',
                'title' => 'Nail & Beauty Expert',
                'avatar_initials' => 'نو',
                'speciality' => 'الأظافر والمكياج',
                'is_home_specialist' => false,
            ],
            [
                'id' => 'sp3',
                'name' => 'خبيرة ريم',
                'title' => 'Home Service Specialist',
                'avatar_initials' => 'ري',
                'speciality' => 'الخدمة المنزلية',
                'is_home_specialist' => true,
            ],
            [
                'id' => 'sp4',
                'name' => 'خبيرة هند',
                'title' => 'Spa & Hammam Expert',
                'avatar_initials' => 'هن',
                'speciality' => 'السبا والحمام',
                'is_home_specialist' => false,
            ],
        ];
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
                'is_weekend' => in_array($dayOfWeek, [5, 6], true), // Friday & Saturday
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
     * Get available (non-conflicting) time slots for a given specialist and date.
     *
     * @param  'salon'|'home'  $bookingType
     * @return array<int, array{time: string, is_available: bool, is_booked: bool}>
     */
    public function getAvailableSlots(string $specialistId, string $dateId, string $bookingType = 'salon'): array
    {
        $bookedMap = $bookingType === 'home'
            ? self::BOOKED_SLOTS_HOME
            : self::BOOKED_SLOTS_SALON;

        $bookedForSpecialist = $bookedMap[$specialistId] ?? [];
        $bookedForDate = $bookedForSpecialist[$dateId] ?? [];

        $slots = [];
        foreach ($this->getAllTimeSlots() as $time) {
            $isBooked = in_array($time, $bookedForDate, true);
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
        $bookedMap = $bookingType === 'home'
            ? self::BOOKED_SLOTS_HOME
            : self::BOOKED_SLOTS_SALON;

        $bookedSlots = $bookedMap[$specialistId][$dateId] ?? [];

        return ! in_array($time, $bookedSlots, true);
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
