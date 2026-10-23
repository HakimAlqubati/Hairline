<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\BookingItem;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\SpecialistShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Centralized, high-performance service for specialist shift scheduling,
 * time-slot generation, and database interval conflict detection.
 */
class SpecialistAvailabilityService
{
    /**
     * Regex pattern for valid Saudi mobile numbers (05XXXXXXXX, +9665XXXXXXXX, 009665XXXXXXXX, 5XXXXXXXX).
     */
    public const SAUDI_PHONE_REGEX = '/^(?:\+966|00966|966|0)?5[0-9]{8}$/';

    /**
     * Booking statuses that occupy a specialist's time slot.
     *
     * @var array<int, string>
     */
    public const ACTIVE_BOOKING_STATUSES = ['confirmed', 'pending', 'in_progress'];

    /**
     * Default slot step interval in minutes when generating shift slots.
     */
    public const DEFAULT_SLOT_STEP_MINUTES = 90;

    /**
     * Convert Eastern Arabic/Persian numerals to Western numerals and strip formatting characters.
     */
    public function sanitizePhoneDigits(string $input): string
    {
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $converted = str_replace($eastern, $western, trim($input));

        return (string) preg_replace('/[\s\-\(\)]+/', '', $converted);
    }

    /**
     * Normalize any valid Saudi mobile number into local 10-digit format (05XXXXXXXX).
     */
    public function normalizeSaudiPhone(string $phone): string
    {
        $clean = $this->sanitizePhoneDigits($phone);

        if (preg_match('/^(?:\+966|00966|966)(5[0-9]{8})$/', $clean, $matches)) {
            return '0'.$matches[1];
        }

        if (preg_match('/^(5[0-9]{8})$/', $clean, $matches)) {
            return '0'.$matches[1];
        }

        return $clean;
    }

    /**
     * Determine whether a phone string matches the Saudi mobile number regex.
     */
    public function isValidSaudiPhone(string $phone): bool
    {
        $clean = $this->sanitizePhoneDigits($phone);

        return (bool) preg_match(self::SAUDI_PHONE_REGEX, $clean);
    }

    /**
     * Resolve an active Specialist by slug or numeric ID.
     */
    public function resolveSpecialist(string|int $identifier): ?Specialist
    {
        $value = trim((string) $identifier);
        if ($value === '') {
            return null;
        }

        return Specialist::query()
            ->where('is_active', true)
            ->where(function ($query) use ($value): void {
                $query->where('slug', $value);
                if (ctype_digit($value)) {
                    $query->orWhere('id', (int) $value);
                }
            })
            ->first();
    }

    /**
     * Resolve a Service by code or numeric ID.
     */
    public function resolveService(string|int|null $identifier): ?Service
    {
        $value = trim((string) $identifier);
        if ($value === '') {
            return null;
        }

        return Service::with('category')
            ->where(function ($query) use ($value): void {
                $query->where('code', $value);
                if (ctype_digit($value)) {
                    $query->orWhere('id', (int) $value);
                }
            })
            ->first();
    }

    /**
     * Resolve effective duration in minutes for a specialist and service (checks custom pivot duration first).
     */
    public function resolveDurationMinutes(Specialist $specialist, ?Service $service = null): int
    {
        if (! $service) {
            return 60;
        }

        $customDuration = DB::table('specialist_service')
            ->where('specialist_id', $specialist->id)
            ->where('service_id', $service->id)
            ->value('custom_duration_minutes');

        $minutes = (int) ($customDuration ?: $service->duration_minutes ?: 60);

        return max(15, $minutes);
    }

    /**
     * Generate upcoming bookable dates starting from today in Asia/Riyadh timezone.
     *
     * @return array<int, array{
     *     id: string,
     *     day_name: string,
     *     date_label: string,
     *     display_label: string,
     *     date_iso: string,
     *     day_of_week: int,
     *     is_today: bool,
     *     is_weekend: bool
     * }>
     */
    public function getUpcomingDates(int $days = 7): array
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

        for ($i = 0; $i < $days; $i++) {
            $date = $today->copy()->addDays($i);
            $dayOfWeek = (int) $date->dayOfWeek;
            $day = (int) $date->format('j');
            $month = (int) $date->format('n');
            $weekdayAr = $arabicDays[$dayOfWeek];
            $dateShort = "{$day} {$arabicMonths[$month]}";

            if ($i === 0) {
                $dayName = 'اليوم';
                $displayLabel = "اليوم - {$weekdayAr}";
            } elseif ($i === 1) {
                $dayName = 'غداً';
                $displayLabel = "غداً - {$weekdayAr}";
            } else {
                $dayName = $weekdayAr;
                $displayLabel = "{$weekdayAr} ({$dateShort})";
            }

            $dates[] = [
                'id' => 'd'.($i + 1),
                'day_name' => $dayName,
                'date_label' => $dateShort,
                'display_label' => $displayLabel,
                'date_iso' => $date->toDateString(),
                'day_of_week' => $dayOfWeek,
                'is_today' => $i === 0,
                'is_weekend' => in_array($dayOfWeek, [5, 6], true),
            ];
        }

        return $dates;
    }

    /**
     * Resolve an ISO date ('2026-10-24'), date code ('d1'), or Arabic label ('غداً - السبت') into a Carbon date.
     */
    public function resolveBookingDate(?string $dateInput): Carbon
    {
        $today = Carbon::today('Asia/Riyadh');
        $clean = trim((string) $dateInput);

        if ($clean === '') {
            return $today->copy()->addDay();
        }

        if (preg_match('/^(\d{4}-\d{2}-\d{2})$/', $clean, $matches)) {
            return Carbon::createFromFormat('Y-m-d', $matches[1], 'Asia/Riyadh')->startOfDay();
        }

        if (preg_match('/^d([1-9]\d*)$/i', $clean, $matches)) {
            $offset = max(0, ((int) $matches[1]) - 1);

            return $today->copy()->addDays($offset);
        }

        foreach ($this->getUpcomingDates(7) as $upcoming) {
            if ($upcoming['display_label'] === $clean || $upcoming['date_label'] === $clean) {
                return Carbon::createFromFormat('Y-m-d', $upcoming['date_iso'], 'Asia/Riyadh')->startOfDay();
            }
        }

        if (str_contains($clean, 'اليوم')) {
            return $today;
        }

        if (str_contains($clean, 'غداً') || str_contains($clean, 'غدا')) {
            return $today->copy()->addDay();
        }

        $dayMap = [
            'الأحد' => Carbon::SUNDAY,
            'الاحد' => Carbon::SUNDAY,
            'الإثنين' => Carbon::MONDAY,
            'الاثنين' => Carbon::MONDAY,
            'الثلاثاء' => Carbon::TUESDAY,
            'الأربعاء' => Carbon::WEDNESDAY,
            'الاربعاء' => Carbon::WEDNESDAY,
            'الخميس' => Carbon::THURSDAY,
            'الجمعة' => Carbon::FRIDAY,
            'السبت' => Carbon::SATURDAY,
        ];

        foreach ($dayMap as $arabicDay => $carbonDay) {
            if (str_contains($clean, $arabicDay)) {
                return $today->copy()->next($carbonDay);
            }
        }

        return $today->copy()->addDay();
    }

    /**
     * Parse an Arabic 12-hour time slot ('05:30 م', '11:30 ص') or 24-hour string ('17:30', '17:30:00') into 'H:i:s'.
     */
    public function parseTimeTo24Hour(?string $timeSlot): ?string
    {
        $raw = $this->sanitizePhoneDigits((string) $timeSlot);
        if ($raw === '') {
            return null;
        }

        $isPm = str_contains((string) $timeSlot, 'م') || stripos((string) $timeSlot, 'pm') !== false;
        $isAm = str_contains((string) $timeSlot, 'ص') || stripos((string) $timeSlot, 'am') !== false;

        $cleaned = trim((string) preg_replace('/[^\d:]/', '', $raw));
        if (! preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $cleaned, $matches)) {
            return null;
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];
        $seconds = isset($matches[3]) ? (int) $matches[3] : 0;

        if ($minutes < 0 || $minutes > 59 || $seconds < 0 || $seconds > 59) {
            return null;
        }

        if ($isPm && $hours < 12) {
            $hours += 12;
        } elseif ($isAm && $hours === 12) {
            $hours = 0;
        }

        if ($hours < 0 || $hours > 23) {
            return null;
        }

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Format a 24-hour time string ('14:00:00') into Arabic 12-hour format ('02:00 م').
     */
    public function format24HourToArabic(string $time24): string
    {
        $parsed = Carbon::createFromFormat('H:i:s', $this->normalizeTimeFormat($time24));

        return $parsed->format('h:i').($parsed->format('A') === 'AM' ? ' ص' : ' م');
    }

    /**
     * Compute end time in 'H:i:s' given a start time and duration in minutes.
     */
    public function calculateEndTime24(string $startTime24, int $durationMinutes): string
    {
        $startMinutes = $this->timeToMinutes($startTime24);
        $endMinutes = min(23 * 60 + 59, $startMinutes + max(15, $durationMinutes));

        return $this->minutesToTime($endMinutes);
    }

    /**
     * Retrieve the configured database shift for a specialist on a specific date's day_of_week.
     */
    public function getShiftForDate(int $specialistId, Carbon $date): ?SpecialistShift
    {
        return SpecialistShift::query()
            ->where('specialist_id', $specialistId)
            ->where('day_of_week', (int) $date->dayOfWeek)
            ->first();
    }

    /**
     * Validate whether a requested time slot falls within the specialist's working shift on the given date.
     *
     * @return array{valid: bool, message: ?string, shift: ?SpecialistShift}
     */
    public function validateWithinShift(
        Specialist $specialist,
        Carbon $date,
        string $startTime24,
        int $durationMinutes = 60,
        ?SpecialistShift $shift = null
    ): array {
        $resolvedShift = $shift ?? $this->getShiftForDate($specialist->id, $date);

        if (! $resolvedShift || $resolvedShift->is_day_off) {
            return [
                'valid' => false,
                'message' => 'الأخصائية المختارة في يوم إجازة في هذا التاريخ ولا توجد فترات عمل متاحة.',
                'shift' => $resolvedShift,
            ];
        }

        $shiftStartMinutes = $this->timeToMinutes($resolvedShift->start_time);
        $shiftEndMinutes = $this->timeToMinutes($resolvedShift->end_time);
        $slotStartMinutes = $this->timeToMinutes($startTime24);
        $requiredShiftWindow = min(max(15, $durationMinutes), 60);

        if ($slotStartMinutes < $shiftStartMinutes || $slotStartMinutes >= $shiftEndMinutes) {
            $startAr = $this->format24HourToArabic($resolvedShift->start_time);
            $endAr = $this->format24HourToArabic($resolvedShift->end_time);

            return [
                'valid' => false,
                'message' => "الوقت المختار يقع خارج أوقات دوام الأخصائية ({$startAr} - {$endAr}).",
                'shift' => $resolvedShift,
            ];
        }

        if (($slotStartMinutes + $requiredShiftWindow) > $shiftEndMinutes) {
            $endAr = $this->format24HourToArabic($resolvedShift->end_time);

            return [
                'valid' => false,
                'message' => "مدة الخدمة تتجاوز وقت انتهاء شيفت الأخصائية ({$endAr}). يرجى اختيار وقت أبكر.",
                'shift' => $resolvedShift,
            ];
        }

        return [
            'valid' => true,
            'message' => null,
            'shift' => $resolvedShift,
        ];
    }

    /**
     * Perform a fast indexed EXISTS query to check if the specialist already has an overlapping booking on the given date.
     */
    public function hasOverlappingBooking(
        int $specialistId,
        Carbon $date,
        string $startTime24,
        string $endTime24,
        ?int $ignoreBookingId = null
    ): bool {
        $normalizedStart = $this->normalizeTimeFormat($startTime24);
        $normalizedEnd = $this->normalizeTimeFormat($endTime24);

        return BookingItem::query()
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.specialist_id', $specialistId)
            ->whereDate('bookings.booking_date', $date->toDateString())
            ->whereIn('bookings.status', self::ACTIVE_BOOKING_STATUSES)
            ->where('booking_items.status', '!=', 'cancelled')
            ->when($ignoreBookingId !== null, fn ($query) => $query->where('bookings.id', '!=', $ignoreBookingId))
            ->where('booking_items.scheduled_start_time', '<', $normalizedEnd)
            ->where('booking_items.scheduled_end_time', '>', $normalizedStart)
            ->exists();
    }

    /**
     * Fetch all active booked time intervals for a specialist on a specific date in a single lightweight query.
     *
     * @return array<int, array{start: string, end: string, start_minutes: int, end_minutes: int}>
     */
    public function getBookedIntervalsForDate(int $specialistId, Carbon $date): array
    {
        $items = BookingItem::query()
            ->select([
                'booking_items.scheduled_start_time',
                'booking_items.scheduled_end_time',
            ])
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.specialist_id', $specialistId)
            ->whereDate('bookings.booking_date', $date->toDateString())
            ->whereIn('bookings.status', self::ACTIVE_BOOKING_STATUSES)
            ->where('booking_items.status', '!=', 'cancelled')
            ->orderBy('booking_items.scheduled_start_time')
            ->get();

        return $items->map(function (BookingItem $item): array {
            $start = $this->normalizeTimeFormat((string) $item->scheduled_start_time);
            $end = $this->normalizeTimeFormat((string) $item->scheduled_end_time);

            return [
                'start' => $start,
                'end' => $end,
                'start_minutes' => $this->timeToMinutes($start),
                'end_minutes' => $this->timeToMinutes($end),
            ];
        })->all();
    }

    /**
     * Build shift-driven time slots for a specialist on a specific date with real-time database booking status.
     *
     * @return array{
     *     specialist_id: string,
     *     date_iso: string,
     *     day_of_week: int,
     *     is_day_off: bool,
     *     shift_start: ?string,
     *     shift_end: ?string,
     *     shift_label: string,
     *     duration_minutes: int,
     *     slots: array<int, array{
     *         time: string,
     *         time_24: string,
     *         end_time_24: string,
     *         is_available: bool,
     *         is_booked: bool
     *     }>,
     *     available_times: array<int, string>
     * }
     */
    public function getAvailabilityForSpecialistDate(
        Specialist $specialist,
        Carbon $date,
        int $durationMinutes = 60,
        ?SpecialistShift $preloadedShift = null,
        ?array $preloadedIntervals = null
    ): array {
        $shift = $preloadedShift ?? $this->getShiftForDate($specialist->id, $date);
        $effectiveDuration = max(15, $durationMinutes);

        if (! $shift || $shift->is_day_off) {
            return [
                'specialist_id' => $specialist->slug,
                'date_iso' => $date->toDateString(),
                'day_of_week' => (int) $date->dayOfWeek,
                'is_day_off' => true,
                'shift_start' => null,
                'shift_end' => null,
                'shift_label' => 'يوم إجازة للأخصائية',
                'duration_minutes' => $effectiveDuration,
                'slots' => [],
                'available_times' => [],
            ];
        }

        $bookedIntervals = $preloadedIntervals ?? $this->getBookedIntervalsForDate($specialist->id, $date);

        $shiftStartMinutes = $this->timeToMinutes($shift->start_time);
        $shiftEndMinutes = $this->timeToMinutes($shift->end_time);
        $stepMinutes = self::DEFAULT_SLOT_STEP_MINUTES;
        $requiredShiftWindow = min($effectiveDuration, 60);

        $slots = [];
        $availableTimes = [];

        $cursorMinutes = $shiftStartMinutes;
        while (($cursorMinutes + $requiredShiftWindow) <= $shiftEndMinutes) {
            $slotStart24 = $this->minutesToTime($cursorMinutes);
            $slotEndMinutes = $cursorMinutes + $effectiveDuration;
            $slotEnd24 = $this->minutesToTime($slotEndMinutes);
            $arabicTime = $this->format24HourToArabic($slotStart24);

            $isBooked = false;
            foreach ($bookedIntervals as $interval) {
                if ($cursorMinutes < $interval['end_minutes'] && $slotEndMinutes > $interval['start_minutes']) {
                    $isBooked = true;
                    break;
                }
            }

            $slots[] = [
                'time' => $arabicTime,
                'time_24' => $slotStart24,
                'end_time_24' => $slotEnd24,
                'is_available' => ! $isBooked,
                'is_booked' => $isBooked,
            ];

            if (! $isBooked) {
                $availableTimes[] = $arabicTime;
            }

            $cursorMinutes += $stepMinutes;
        }

        $shiftStartAr = $this->format24HourToArabic($shift->start_time);
        $shiftEndAr = $this->format24HourToArabic($shift->end_time);

        return [
            'specialist_id' => $specialist->slug,
            'date_iso' => $date->toDateString(),
            'day_of_week' => (int) $date->dayOfWeek,
            'is_day_off' => false,
            'shift_start' => $this->normalizeTimeFormat($shift->start_time),
            'shift_end' => $this->normalizeTimeFormat($shift->end_time),
            'shift_label' => "من {$shiftStartAr} إلى {$shiftEndAr}",
            'duration_minutes' => $effectiveDuration,
            'slots' => $slots,
            'available_times' => $availableTimes,
        ];
    }

    /**
     * Ensure time string is normalized to 'H:i:s'.
     */
    private function normalizeTimeFormat(string $time): string
    {
        $trimmed = trim($time);
        if (preg_match('/^\d{2}:\d{2}$/', $trimmed)) {
            return $trimmed.':00';
        }

        return $trimmed;
    }

    /**
     * Convert 'H:i:s' or 'H:i' into minutes since midnight.
     */
    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $this->normalizeTimeFormat($time));
        $hours = (int) ($parts[0] ?? 0);
        $minutes = (int) ($parts[1] ?? 0);

        return ($hours * 60) + $minutes;
    }

    /**
     * Convert minutes since midnight into 'H:i:s'.
     */
    private function minutesToTime(int $totalMinutes): string
    {
        $clamped = max(0, min(23 * 60 + 59, $totalMinutes));
        $hours = intdiv($clamped, 60);
        $minutes = $clamped % 60;

        return sprintf('%02d:%02d:00', $hours, $minutes);
    }
}
