<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'membership_tier_id' => MembershipTier::inRandomOrder()->value('id') ?? MembershipTier::factory(),
            'customer_code' => 'HL-'.fake()->unique()->numberBetween(3000, 9999),
            'name' => fake()->name(),
            'phone' => '05'.fake()->unique()->numberBetween(10000000, 99999999),
            'email' => fake()->safeEmail(),
            'avatar_url' => null,
            'total_points' => fake()->numberBetween(0, 3000),
            'is_active' => true,
        ];
    }
}
