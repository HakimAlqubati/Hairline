<?php

declare(strict_types=1);

use App\Filament\Resources\SiteVisits\Pages\ListSiteVisits;
use App\Models\Customer;
use App\Models\SiteVisit;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

test('public page visit records device type, os, browser, page, external referer, and timestamp', function () {
    $this->travelTo(Carbon::parse('2026-11-16 10:15:00', 'UTC'));

    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
        'Referer' => 'https://www.instagram.com/hairline.ksa/',
    ])->get('/services');

    $response->assertOk();

    $visit = SiteVisit::query()->first();

    expect($visit)->not->toBeNull()
        ->and($visit->device_type)->toBe(SiteVisit::DEVICE_MOBILE)
        ->and($visit->device_name)->toBe('iPhone')
        ->and($visit->os)->toBe('iOS')
        ->and($visit->browser)->toBe('Safari')
        ->and($visit->page_name)->toBe('services')
        ->and($visit->path)->toBe('/services')
        ->and($visit->referer_host)->toBe('www.instagram.com')
        ->and($visit->customer_id)->toBeNull()
        ->and($visit->visited_at->format('Y-m-d H:i:s'))->toBe('2026-11-16 10:15:00');
});

test('repeated page visits from the same visitor are each stored without blocking', function () {
    $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    ];

    $this->withHeaders($headers)->get('/')->assertOk();
    $this->withHeaders($headers)->get('/')->assertOk();

    expect(SiteVisit::query()->count())->toBe(2);
});

test('authenticated customer visit associates customer_id cleanly', function () {
    $customer = Customer::factory()->create(['name' => 'نورة القحطاني']);

    $response = $this->withSession(['logged_in_customer_id' => $customer->id])
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
        ])
        ->get('/booking');

    $response->assertOk();

    $this->assertDatabaseHas('site_visits', [
        'customer_id' => $customer->id,
        'page_name' => 'booking',
        'path' => '/booking',
        'device_type' => SiteVisit::DEVICE_MOBILE,
        'device_name' => 'Samsung',
        'os' => 'Android',
        'browser' => 'Chrome',
    ]);
});

test('search bots, ajax endpoints, prefetch requests, and admin routes are not tracked as site visits', function () {
    $user = User::factory()->create();

    $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    ])->get('/')->assertOk();

    $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0.0.0',
        'Sec-Purpose' => 'prefetch',
    ])->get('/services')->assertOk();

    $this->getJson('/services/suggestions?q=قص')->assertOk();

    $this->actingAs($user)->get('/admin')->assertOk();

    expect(SiteVisit::query()->count())->toBe(0);
});

test('unauthenticated user is redirected to admin login when accessing site visits table', function () {
    $this->get('/admin/site-visits')->assertRedirect('/admin/login');
});

test('authenticated admin sees clear site visits table, stats overview, and riyadh time without forms', function () {
    $this->travelTo(Carbon::parse('2026-11-16 12:00:00', 'UTC'));

    $user = User::factory()->create();
    $customer = Customer::factory()->create(['name' => 'سارة العتيبي']);

    SiteVisit::factory()->create([
        'device_type' => SiteVisit::DEVICE_MOBILE,
        'device_name' => 'iPhone',
        'os' => 'iOS',
        'browser' => 'Instagram',
        'page_name' => 'booking',
        'path' => '/booking',
        'referer_host' => 'instagram.com',
        'customer_id' => $customer->id,
        'visited_at' => Carbon::parse('2026-11-16 10:30:00', 'UTC'),
    ]);

    SiteVisit::factory()->create([
        'device_type' => SiteVisit::DEVICE_DESKTOP,
        'device_name' => 'Windows PC',
        'os' => 'Windows',
        'browser' => 'Chrome',
        'page_name' => 'home',
        'path' => '/',
        'referer_host' => null,
        'customer_id' => null,
        'visited_at' => Carbon::parse('2026-11-15 08:00:00', 'UTC'),
    ]);

    app()->setLocale('ar');

    $response = $this->actingAs($user)->get('/admin/site-visits');

    $response->assertOk()
        ->assertSee('سجل زوار الموقع')
        ->assertSee('زيارات اليوم')
        ->assertSee('زيارات الجوال')
        ->assertSee('2026-11-16 13:30')
        ->assertSee('iPhone')
        ->assertSee('Instagram')
        ->assertSee('صفحة الحجز')
        ->assertSee('سارة العتيبي')
        ->assertSee('زيارة مباشرة');
});

test('site visits resource exposes no create, edit, or view routes', function () {
    $user = User::factory()->create();
    $visit = SiteVisit::factory()->create();

    $this->actingAs($user)->get('/admin/site-visits/create')->assertNotFound();
    $this->actingAs($user)->get("/admin/site-visits/{$visit->id}/edit")->assertNotFound();
    $this->actingAs($user)->get("/admin/site-visits/{$visit->id}")->assertNotFound();
});

test('site visits table can filter records by device type and today visits', function () {
    $this->travelTo(Carbon::parse('2026-11-16 12:00:00', 'UTC'));

    $user = User::factory()->create();

    $todayMobile = SiteVisit::factory()->create([
        'device_type' => SiteVisit::DEVICE_MOBILE,
        'device_name' => 'iPhone',
        'os' => 'iOS',
        'visited_at' => Carbon::parse('2026-11-16 09:00:00', 'UTC'),
    ]);

    $yesterdayDesktop = SiteVisit::factory()->create([
        'device_type' => SiteVisit::DEVICE_DESKTOP,
        'device_name' => 'Windows PC',
        'os' => 'Windows',
        'visited_at' => Carbon::parse('2026-11-15 09:00:00', 'UTC'),
    ]);

    Livewire::actingAs($user)
        ->test(ListSiteVisits::class)
        ->assertCanSeeTableRecords([$todayMobile, $yesterdayDesktop])
        ->filterTable('device_type', SiteVisit::DEVICE_MOBILE)
        ->assertCanSeeTableRecords([$todayMobile])
        ->assertCanNotSeeTableRecords([$yesterdayDesktop])
        ->resetTableFilters()
        ->filterTable('today', true)
        ->assertCanSeeTableRecords([$todayMobile])
        ->assertCanNotSeeTableRecords([$yesterdayDesktop]);
});
