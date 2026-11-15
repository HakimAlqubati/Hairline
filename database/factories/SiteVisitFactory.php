<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SiteVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteVisit>
 */
class SiteVisitFactory extends Factory
{
    protected $model = SiteVisit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $deviceType = fake()->randomElement([
            SiteVisit::DEVICE_MOBILE,
            SiteVisit::DEVICE_DESKTOP,
            SiteVisit::DEVICE_TABLET,
        ]);

        [$deviceName, $os, $browser] = match ($deviceType) {
            SiteVisit::DEVICE_MOBILE => fake()->randomElement([
                ['iPhone', 'iOS', 'Safari'],
                ['Samsung', 'Android', 'Chrome'],
                ['iPhone', 'iOS', 'Instagram'],
            ]),
            SiteVisit::DEVICE_TABLET => fake()->randomElement([
                ['iPad', 'iPadOS', 'Safari'],
                ['Android Tablet', 'Android', 'Chrome'],
            ]),
            default => fake()->randomElement([
                ['Windows PC', 'Windows', 'Chrome'],
                ['Mac', 'macOS', 'Safari'],
                ['Windows PC', 'Windows', 'Edge'],
            ]),
        };

        $page = fake()->randomElement([
            ['home', '/'],
            ['services', '/services'],
            ['specialists', '/specialists'],
            ['booking', '/booking'],
            ['about', '/about'],
            ['contact', '/contact'],
        ]);

        return [
            'device_type' => $deviceType,
            'device_name' => $deviceName,
            'os' => $os,
            'browser' => $browser,
            'page_name' => $page[0],
            'path' => $page[1],
            'referer_host' => fake()->optional(0.4)->randomElement(['instagram.com', 'google.com', 'snapchat.com']),
            'ip_address' => fake()->ipv4(),
            'session_id' => fake()->uuid(),
            'customer_id' => null,
            'user_agent' => fake()->userAgent(),
            'visited_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
