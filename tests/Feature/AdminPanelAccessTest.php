<?php

declare(strict_types=1);

use App\Models\User;

test('authenticated user can access filament admin dashboard without navigation exception', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin');

    $response->assertSuccessful();
});

test('authenticated user can access filament resource index pages without action or syntax errors', function (string $url) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get($url);

    $response->assertSuccessful();
})->with([
    '/admin/bookings',
    '/admin/services',
    '/admin/service-categories',
    '/admin/service-groups',
    '/admin/hair-lengths',
    '/admin/catalog-pages',
    '/admin/specialists',
    '/admin/specialist-shifts',
    '/admin/specialist-unavailabilities',
    '/admin/customers',
    '/admin/membership-tiers',
    '/admin/loyalty-point-transactions',
    '/admin/hero-slides',
    '/admin/salon-galleries',
    '/admin/founder-stories',
    '/admin/cancellation-policies',
    '/admin/salon-working-hours',
    '/admin/payments',
    '/admin/payment-methods',
    '/admin/districts',
    '/admin/salon-settings',
    '/admin/users',
]);
