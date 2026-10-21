<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $nameAr = fake()->word().' معالجة شعر';
        $nameEn = fake()->word().' Hair Treatment';

        return [
            'category_id' => ServiceCategory::inRandomOrder()->value('id') ?? 1,
            'group_id' => null,
            'code' => 'srv_'.Str::random(8),
            'name' => [
                'ar' => $nameAr,
                'en' => $nameEn,
            ],
            'inclusions' => null,
            'description' => [
                'ar' => 'خدمة مميزة للعناية والترطيب بأحدث المنتجات.',
                'en' => 'Premium salon care session with top-tier products.',
            ],
            'price_display_override' => null,
            'base_price' => fake()->randomElement([150.0, 250.0, 350.0, 500.0]),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
            'buffer_minutes' => 10,
            'has_length_pricing' => false,
            'is_popular' => fake()->boolean(25),
            'is_home_available' => fake()->boolean(50),
            'sort_order' => fake()->numberBetween(1, 50),
            'is_active' => true,
        ];
    }
}
