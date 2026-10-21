<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $date = Carbon::today()->addDays(fake()->numberBetween(1, 14));
        $subtotal = fake()->randomElement([200.0, 350.0, 500.0, 750.0]);
        $vat = round($subtotal * 0.15, 2);
        $total = $subtotal + $vat;
        $deposit = round($total * 0.30, 2);

        return [
            'booking_number' => (string) fake()->unique()->numberBetween(100, 999),
            'customer_id' => Customer::inRandomOrder()->value('id') ?? Customer::factory(),
            'customer_address_id' => null,
            'booking_type' => fake()->randomElement(['salon', 'home']),
            'booking_date' => $date->toDateString(),
            'overall_start_time' => '15:00:00',
            'overall_end_time' => '17:00:00',
            'status' => 'confirmed',
            'subtotal_amount' => $subtotal,
            'discount_amount' => 0.00,
            'vat_amount' => $vat,
            'total_amount' => $total,
            'deposit_required' => $deposit,
            'deposit_paid' => $deposit,
            'payment_status' => 'partially_paid',
            'admin_notes' => null,
        ];
    }
}
