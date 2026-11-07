<?php

declare(strict_types=1);

use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\ServiceCategories\Pages\EditServiceCategory;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\ServiceCategory;
use App\Models\User;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Livewire\Livewire;

test('authenticated user can access filament admin dashboard without navigation exception', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin');

    $response->assertSuccessful();
    $response->assertDontSee(__('admin.charts.revenue_trend'));
    $response->assertSee(__('admin.charts.bookings_status'));
    $response->assertSee(__('admin.charts.top_services'));
    $response->assertSee(__('admin.charts.top_specialists'));
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
    '/admin/customers',
    '/admin/membership-tiers',
    '/admin/loyalty-point-transactions',
    '/admin/hero-slides',
    '/admin/settings',
    '/admin/users',
]);

test('authenticated user is forbidden from hidden or disabled filament resource index pages', function (string $url) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get($url);

    $response->assertForbidden();
})->with([
    '/admin/payments',
    '/admin/payment-methods',
    '/admin/districts',
    '/admin/salon-galleries',
    '/admin/cancellation-policies',
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
    '/admin/customers/create',
    '/admin/membership-tiers/create',
    '/admin/loyalty-point-transactions/create',
    '/admin/hero-slides/create',
    '/admin/users/create',
]);

test('authenticated user is forbidden from hidden or disabled filament resource create pages', function (string $url) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get($url);

    $response->assertForbidden();
})->with([
    '/admin/payments/create',
    '/admin/payment-methods/create',
    '/admin/districts/create',
    '/admin/salon-galleries/create',
    '/admin/cancellation-policies/create',
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

test('booking edit page handles translatable admin notes without object string errors', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    $booking = Booking::create([
        'booking_number' => 'HL-TEST-001',
        'customer_id' => $customer->id,
        'booking_type' => 'salon',
        'booking_date' => now()->toDateString(),
        'overall_start_time' => '10:00:00',
        'overall_end_time' => '11:00:00',
        'status' => 'confirmed',
        'subtotal_amount' => 100,
        'total_amount' => 100,
        'admin_notes' => [
            'ar' => 'عميلة مميزة تفضل قهوة عربية',
            'en' => 'VIP customer prefers arabic coffee',
        ],
    ]);

    $response = $this->actingAs($user)->get("/admin/bookings/{$booking->id}/edit");

    $response->assertSuccessful();

    Livewire::actingAs($user)
        ->test(EditBooking::class, [
            'record' => $booking->id,
        ])
        ->assertSchemaStateSet([
            'admin_notes.ar' => 'عميلة مميزة تفضل قهوة عربية',
            'admin_notes.en' => 'VIP customer prefers arabic coffee',
        ]);
});

test('language switcher can switch locale between arabic and english', function () {
    LanguageSwitch::switchLocale('ar');
    expect(session('locale'))->toBe('ar');

    LanguageSwitch::switchLocale('en');
    expect(session('locale'))->toBe('en');
});

test('unauthenticated user can access custom filament login page and see salon branding and images', function () {
    $response = $this->get('/admin/login');

    $response->assertSuccessful();
    $response->assertSee(__('admin.brand.name'));
    $response->assertSee('reception-desk.jpg');
    $response->assertSee('logo.png');
});

test('user can authenticate via custom filament login page', function () {
    $user = User::factory()->create([
        'email' => 'admin@hairline.test',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'admin@hairline.test',
            'password' => 'password123',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');

    $this->assertAuthenticatedAs($user);
});

test('invalid credentials show validation error on custom filament login page', function () {
    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'wrong@email.com',
            'password' => 'wrongpassword',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);
});

test('booking index page does not show payment status or total amount columns', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    Booking::create([
        'booking_number' => 'HL-TEST-FIN-001',
        'customer_id' => $customer->id,
        'booking_type' => 'salon',
        'booking_date' => now()->toDateString(),
        'overall_start_time' => '10:00:00',
        'overall_end_time' => '11:00:00',
        'status' => 'confirmed',
        'subtotal_amount' => 500.00,
        'total_amount' => 500.00,
        'payment_status' => 'fully_paid',
    ]);

    $response = $this->actingAs($user)->get('/admin/bookings');

    $response->assertSuccessful();
    $response->assertDontSee(__('admin.fields.payment_status'));
    $response->assertDontSee(__('admin.fields.total_amount'));
});

test('booking create and edit pages do not show financial summary or payment fields', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    $booking = Booking::create([
        'booking_number' => 'HL-TEST-FIN-002',
        'customer_id' => $customer->id,
        'booking_type' => 'salon',
        'booking_date' => now()->toDateString(),
        'overall_start_time' => '10:00:00',
        'overall_end_time' => '11:00:00',
        'status' => 'confirmed',
        'subtotal_amount' => 350.00,
        'total_amount' => 350.00,
        'payment_status' => 'unpaid',
    ]);

    $createResponse = $this->actingAs($user)->get('/admin/bookings/create');
    $createResponse->assertSuccessful();
    $createResponse->assertDontSee(__('admin.sections.financial_summary'));
    $createResponse->assertDontSee(__('admin.fields.subtotal_amount'));
    $createResponse->assertDontSee(__('admin.fields.vat_amount'));

    $editResponse = $this->actingAs($user)->get("/admin/bookings/{$booking->id}/edit");
    $editResponse->assertSuccessful();
    $editResponse->assertDontSee(__('admin.sections.financial_summary'));
    $editResponse->assertDontSee(__('admin.fields.subtotal_amount'));
    $editResponse->assertDontSee(__('admin.fields.total_amount'));
    $editResponse->assertDontSee(__('admin.fields.deposit_required'));
});
