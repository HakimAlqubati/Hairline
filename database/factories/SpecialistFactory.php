<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Specialist;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Specialist>
 */
class SpecialistFactory extends Factory
{
    protected $model = Specialist::class;

    public function definition(): array
    {
        $name = fake()->firstNameFemale();

        return [
            'slug' => 'sp_'.Str::slug($name).'_'.fake()->unique()->randomNumber(3),
            'name' => [
                'ar' => $name,
                'en' => $name,
            ],
            'role' => [
                'ar' => 'أخصائية تجميل وعناية',
                'en' => 'Beauty & Care Specialist',
            ],
            'experience' => [
                'ar' => 'خبرة لأكثر من '.fake()->numberBetween(5, 15).' سنوات',
                'en' => fake()->numberBetween(5, 15).'+ Years Experience',
            ],
            'bio' => [
                'ar' => 'متخصصة في العناية الفائقة بأرقى المعايير.',
                'en' => 'Specialized in top-tier hair and beauty techniques.',
            ],
            'avatar_url' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=211C18&color=C29E6D&bold=true',
            'phone' => '0548857705',
            'is_home_specialist' => fake()->boolean(40),
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
