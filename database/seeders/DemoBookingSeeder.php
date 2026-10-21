<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\Specialist;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoBookingSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::first();
        $specialist = Specialist::where('slug', 'sp_karen')->first() ?? Specialist::first();
        $service = Service::where('name->ar', 'البروتين بالتمليس الحراري')->first() ?? Service::first();
        $paymentMethod = PaymentMethod::where('code', 'mada')->first() ?? PaymentMethod::first();

        if (! $customer || ! $specialist || ! $service) {
            return;
        }

        $bookingDate = Carbon::today()->addDays(2)->toDateString();
        $startTime = '17:30:00';
        $endTime = '20:30:00';

        $booking = Booking::updateOrCreate(
            ['booking_number' => '482'],
            [
                'customer_id' => $customer->id,
                'customer_address_id' => null,
                'booking_type' => 'salon',
                'booking_date' => $bookingDate,
                'overall_start_time' => $startTime,
                'overall_end_time' => $endTime,
                'status' => 'confirmed',
                'subtotal_amount' => 750.00,
                'discount_amount' => 0.00,
                'vat_amount' => 97.83,
                'total_amount' => 750.00,
                'deposit_required' => 225.00,
                'deposit_paid' => 225.00,
                'payment_status' => 'partially_paid',
                'admin_notes' => [
                    'ar' => 'حجز مؤكد مع دفعة عربون عبر بطاقة مدى',
                    'en' => 'Confirmed booking with deposit paid via Mada',
                ],
            ]
        );

        BookingItem::updateOrCreate(
            [
                'booking_id' => $booking->id,
                'service_id' => $service->id,
            ],
            [
                'specialist_id' => $specialist->id,
                'hair_length_id' => null,
                'unit_price' => 750.00,
                'duration_minutes' => 180,
                'scheduled_start_time' => $startTime,
                'scheduled_end_time' => $endTime,
                'status' => 'confirmed',
            ]
        );

        if ($paymentMethod) {
            Payment::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'payment_type' => 'deposit',
                ],
                [
                    'payment_method_id' => $paymentMethod->id,
                    'transaction_reference' => 'TXN-98421035',
                    'amount' => 225.00,
                    'status' => 'success',
                    'paid_at' => Carbon::now(),
                ]
            );
        }
    }
}
