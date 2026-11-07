<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Customer;
use Carbon\Carbon;

class BookingPayloadFormatter
{
    /**
     * Format Customer model into frontend profile array.
     *
     * @return array{id: int, customer_code: string, name: string, phone: ?string, email: ?string, avatar_url: string}
     */
    public function formatCustomerPayload(Customer $customer): array
    {
        $avatar = $customer->avatar_url;
        if ($avatar && ! str_starts_with($avatar, 'http://') && ! str_starts_with($avatar, 'https://') && ! str_starts_with($avatar, 'data:')) {
            $avatar = (str_starts_with($avatar, 'images/') || str_starts_with($avatar, '/images/'))
                ? asset(ltrim($avatar, '/'))
                : asset('storage/'.ltrim($avatar, '/'));
        }

        return [
            'id' => $customer->id,
            'customer_code' => $customer->customer_code,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'avatar_url' => $avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=211C18&color=C5A373&bold=true',
        ];
    }

    /**
     * Return all recent active bookings belonging to the given customer.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCustomerBookingsPayload(Customer $customer): array
    {
        return Booking::with(['customer', 'items.service.category', 'items.specialist'])
            ->where('customer_id', $customer->id)
            ->whereIn('status', [
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_PENDING,
                Booking::STATUS_IN_PROGRESS,
                Booking::STATUS_COMPLETED,
            ])
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function (Booking $booking) use ($customer): array {
                $firstItem = $booking->items->first();
                $service = $firstItem?->service;
                $specialist = $firstItem?->specialist;

                $startTimeFormatted = '05:30 م';
                if ($booking->overall_start_time) {
                    $parsedTime = Carbon::parse($booking->overall_start_time);
                    $startTimeFormatted = $parsedTime->format('h:i').($parsedTime->format('A') === 'AM' ? ' ص' : ' م');
                }

                return [
                    'id' => $booking->booking_number,
                    'db_id' => $booking->id,
                    'status' => $booking->status,
                    'customerName' => $customer->name,
                    'customerPhone' => $customer->phone,
                    'customerEmail' => $customer->email,
                    'categoryTitle' => $service?->category?->getTranslation('name', 'ar') ?? '',
                    'serviceName' => $service?->getTranslation('name', 'ar') ?? '',
                    'specialistName' => $specialist ? $specialist->getTranslation('name', 'ar') : '',
                    'date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '',
                    'time' => $startTimeFormatted,
                    'price' => ((int) $booking->total_amount).' ر.س',
                    'guests_count' => $booking->guests_count ?? 1,
                    'customer_notes' => $booking->customer_notes,
                ];
            })
            ->values()
            ->all();
    }
}
