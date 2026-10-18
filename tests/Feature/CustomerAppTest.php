<?php

declare(strict_types=1);

use App\Services\FrontData\BookingCalendarDataService;
use App\Services\FrontData\CartAndCheckoutDataService;
use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\CustomerAccountDataService;
use App\Services\FrontData\HomeServiceDataService;
use App\Services\FrontData\SalonInfoDataService;

describe('Customer-Facing Pages', function () {
    test('home page returns 200 and renders brand identity', function () {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('HAIRLINE');
        $response->assertSee('هيرلاين');
        $response->assertSee('BEAUTY LOUNGE');
        $response->assertSee('جميع الخدمات');
        $response->assertSee('الخدمات المتاحة للحجز');
        $response->assertSee('أناقتكِ تتجدد');
        $response->assertSee('عن هيرلاين');
        $response->assertSee('حسابي');
    });

    test('about page returns 200 and renders bio, hours, and location', function () {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('هيرلاين');
        $response->assertSee('HAIRLINE BEAUTY LOUNGE');
        $response->assertSee('من نحن');
        $response->assertSee('أوقات العمل الرسمية');
        $response->assertSee('التعريف بالصالون');
    });

    test('services catalog page returns 200 and lists categories', function () {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('جميع الخدمات (زيارة فورية)');
        $response->assertSee('الاستشوار والتصفيف الفوري');
        $response->assertSee('المناكير والبادكير والسبا');
        $response->assertSee('قص الشعر والعناية بالملامح');
    });

    test('booking page returns 200 and shows all steps', function () {
        $response = $this->get('/booking');

        $response->assertStatus(200);
        $response->assertSee('الخدمات المتاحة للحجز');
        $response->assertSee('المعالجات الحارة');
        $response->assertSee('المعالجات الباردة والترميم');
        $response->assertSee('الصبغات والتساريح');
        $response->assertSee('العناية بالفروة والمساج');
    });

    test('profile page returns 200 and shows member profile and bookings', function () {
        $response = $this->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('أهلاً بكِ، عميلتنا المميزة');
        $response->assertSee('VIP');
        $response->assertSee('عضوية هيرلاين الذهبية');
        $response->assertSee('حجوزاتي المؤكدة');
    });
});

describe('Static Data Services', function () {
    test('SalonInfoDataService returns complete identity and location', function () {
        $service = app(SalonInfoDataService::class);

        $identity = $service->getSalonIdentity();
        expect($identity['name_en'])->toBe('HAIRLINE');
        expect($identity['name_ar'])->toBe('هيرلاين');
        expect($identity['hero_slogans'])->toHaveCount(2);

        $hours = $service->getWorkingHours();
        expect($hours['days'])->toBe('يومياً');

        $location = $service->getLocation();
        expect($location['city'])->toBe('الرياض');
        expect($location['district'])->toBe('حي الملقا');
    });

    test('CatalogDataService provides 7 categories and search works', function () {
        $service = app(CatalogDataService::class);

        $categories = $service->getAllCategories();
        expect($categories)->toHaveCount(7);

        $serviceDetails = $service->findServiceById('hs_full_cut');
        expect($serviceDetails)->not->toBeNull();
        expect($serviceDetails['price'])->toBe(80);

        $searchResults = $service->searchServices('بروتين');
        expect($searchResults)->not->toBeEmpty();

        $totals = $service->calculateTotals(['hs_full_cut', 'nl_manicure']);
        expect($totals['total_price'])->toBe(150);
        expect($totals['count'])->toBe(2);
    });

    test('HomeServiceDataService covers 12 districts across 3 zones', function () {
        $service = app(HomeServiceDataService::class);

        $allDistricts = $service->getAllDistricts();
        expect($allDistricts)->toHaveCount(12);

        $availability = $service->checkDistrictAvailability('d_malqa');
        expect($availability['is_covered'])->toBeTrue();

        $unavailable = $service->checkDistrictAvailability('d_hittin');
        expect($unavailable['is_covered'])->toBeFalse();
    });

    test('BookingCalendarDataService generates dates and checks slots', function () {
        $service = app(BookingCalendarDataService::class);

        $dates = $service->getAvailableDates();
        expect($dates)->toHaveCount(7);
        expect($dates[0]['day_name'])->toBe('اليوم');

        $slots = $service->getAvailableSlots('sp1', 'd1');
        expect($slots)->not->toBeEmpty();
    });

    test('CartAndCheckoutDataService calculates deposit at 30 percent', function () {
        $service = app(CartAndCheckoutDataService::class);

        $deposit = $service->calculateDeposit(300);
        expect($deposit['deposit_amount'])->toBe(90);
        expect($deposit['deposit_percentage'])->toBe(30);
        expect($deposit['remaining_amount'])->toBe(210);

        // Minimum deposit floor (50 SAR)
        $lowDeposit = $service->calculateDeposit(100);
        expect($lowDeposit['deposit_amount'])->toBe(50);
    });

    test('CustomerAccountDataService validates login by code and phone', function () {
        $service = app(CustomerAccountDataService::class);

        $codeLogin = $service->login('HL-1001');
        expect($codeLogin['success'])->toBeTrue();
        expect($codeLogin['customer']['name'])->toBe('سارة العتيبي');

        $phoneLogin = $service->login('0551234567');
        expect($phoneLogin['success'])->toBeTrue();

        $failedLogin = $service->login('INVALID');
        expect($failedLogin['success'])->toBeFalse();
    });
});
