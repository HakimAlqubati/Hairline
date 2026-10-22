<?php

declare(strict_types=1);

use App\Filament\Resources\ServiceCategories\Pages\EditServiceCategory;
use App\Models\ServiceCategory;
use App\Models\User;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Livewire\Livewire;

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

test('authenticated user can access filament resource create pages without schema or syntax errors', function (string $url) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get($url);

    $response->assertSuccessful();
})->with([
    '/admin/bookings/create',
    '/admin/services/create',
    '/admin/service-categories/create',
    '/admin/service-groups/create',
    '/admin/hair-lengths/create',
    '/admin/catalog-pages/create',
    '/admin/specialists/create',
    '/admin/specialist-shifts/create',
    '/admin/specialist-unavailabilities/create',
    '/admin/customers/create',
    '/admin/membership-tiers/create',
    '/admin/loyalty-point-transactions/create',
    '/admin/hero-slides/create',
    '/admin/salon-galleries/create',
    '/admin/founder-stories/create',
    '/admin/cancellation-policies/create',
    '/admin/salon-working-hours/create',
    '/admin/payments/create',
    '/admin/payment-methods/create',
    '/admin/districts/create',
    '/admin/salon-settings/create',
    '/admin/users/create',
]);

test('authenticated user can access service category edit page without schema or syntax errors', function () {
    $user = User::factory()->create();
    $category = ServiceCategory::create([
        'slug' => 'test-category',
        'name' => [
            'ar' => 'عناية بالشعر',
            'en' => 'Hair Care',
        ],
        'subtitle' => [
            'ar' => 'خدمات متكاملة واحترافية',
            'en' => 'Complete and professional services',
        ],
        'icon' => 'scissors',
        'page_ref' => 1,
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get("/admin/service-categories/{$category->id}/edit");

    $response->assertSuccessful();

    Livewire::actingAs($user)
        ->test(EditServiceCategory::class, [
            'record' => $category->id,
        ])
        ->assertSchemaStateSet([
            'name.ar' => 'عناية بالشعر',
            'name.en' => 'Hair Care',
            'subtitle.ar' => 'خدمات متكاملة واحترافية',
            'subtitle.en' => 'Complete and professional services',
        ]);
});

test('language switcher can switch locale between arabic and english', function () {
    LanguageSwitch::switchLocale('ar');
    expect(session('locale'))->toBe('ar');

    LanguageSwitch::switchLocale('en');
    expect(session('locale'))->toBe('en');
});
