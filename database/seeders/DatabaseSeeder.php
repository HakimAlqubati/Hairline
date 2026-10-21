<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@hairlinesalon.sa'],
            [
                'name' => 'Hairline Admin',
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            SalonSetupSeeder::class,
            CatalogSeeder::class,
            SpecialistSeeder::class,
            HomeServiceSeeder::class,
            CustomerAndLoyaltySeeder::class,
            PolicyAndPaymentSeeder::class,
            DemoBookingSeeder::class,
        ]);
    }
}
