<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\Customer;

/**
 * Database-backed customer account data for Hairline Beauty Lounge.
 */
class CustomerAccountDataService
{
    /**
     * Attempt login by customer code or phone number.
     *
     * @return array{
     *     success: bool,
     *     customer: array<string, mixed>|null,
     *     message: string
     * }
     */
    public function login(string $identifier): array
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return [
                'success' => false,
                'customer' => null,
                'message' => 'يرجى إدخال كود العميلة أو رقم الجوال.',
            ];
        }

        $customer = Customer::with('membershipTier')
            ->where('customer_code', strtoupper($identifier))
            ->orWhere('phone', $identifier)
            ->first();

        if (! $customer) {
            return [
                'success' => false,
                'customer' => null,
                'message' => 'لم يتم العثور على الحساب. تأكدي من كود العميلة أو رقم الجوال.',
            ];
        }

        $tier = $customer->membershipTier;

        return [
            'success' => true,
            'customer' => [
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'member_since' => $customer->created_at?->format('Y-m') ?? '2024-03',
                'tier' => $tier ? $tier->getTranslation('name', 'en') : 'Silver',
                'tier_name_ar' => $tier ? $tier->getTranslation('name', 'ar') : 'فضية',
                'tier_color' => $tier?->badge_color ?? '#C5A373',
                'points' => (int) $customer->loyalty_points_balance,
            ],
            'message' => 'أهلاً بعودتكِ! تم تسجيل الدخول بنجاح.',
        ];
    }

    /**
     * Get the booking history for a customer code from the database.
     *
     * @return array<int, array{
     *     ref_code: string,
     *     date_label: string,
     *     time: string,
     *     services: string[],
     *     total_sar: int,
     *     status: string,
     *     status_label_ar: string,
     *     booking_type: string
     * }>
     */
    public function getBookingHistory(string $customerCode): array
    {
        $customer = Customer::where('customer_code', strtoupper(trim($customerCode)))->first();
        if (! $customer) {
            return [];
        }

        $bookings = $customer->bookings()->with('items.service')->latest('booking_date')->get();

        return $bookings->map(function ($b) {
            $serviceNames = $b->items->map(fn ($it) => $it->service?->getTranslation('name', 'ar') ?? 'خدمة')->all();
            $statusLabels = [
                'confirmed' => 'مؤكد',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
                'pending' => 'قيد الانتظار',
            ];

            return [
                'ref_code' => '#'.$b->booking_number,
                'date_label' => $b->booking_date?->translatedFormat('l، j F Y') ?? '',
                'time' => $b->overall_start_time ?? '',
                'services' => $serviceNames,
                'total_sar' => (int) $b->total_amount,
                'status' => $b->status,
                'status_label_ar' => $statusLabels[$b->status] ?? $b->status,
                'booking_type' => $b->booking_type,
            ];
        })->all();
    }

    /**
     * Get all invoices for a customer code from the database.
     */
    public function getInvoices(string $customerCode): array
    {
        $customer = Customer::where('customer_code', strtoupper(trim($customerCode)))->first();
        if (! $customer) {
            return [];
        }

        $bookings = $customer->bookings()->with(['items.service', 'payments'])->get();

        return $bookings->map(function ($b) {
            $items = $b->items->map(fn ($it) => [
                'name' => $it->service?->getTranslation('name', 'ar') ?? 'خدمة صالون',
                'price' => (int) $it->unit_price,
            ])->all();

            $isPaid = $b->payments->where('status', 'successful')->isNotEmpty() || $b->payment_status === 'paid';

            return [
                'invoice_no' => 'INV-'.$b->booking_number,
                'date' => $b->created_at?->format('d/m/Y') ?? '',
                'ref_code' => '#'.$b->booking_number,
                'items' => $items,
                'subtotal' => (int) $b->subtotal_amount,
                'visit_fee' => 0,
                'total' => (int) $b->total_amount,
                'paid' => $isPaid,
            ];
        })->all();
    }
}
