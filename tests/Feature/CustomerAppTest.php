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
        $response->assertSee('فخامة الجمال');
        $response->assertSee('عن هيرلاين');
        $response->assertSee('الخدمات');
        $response->assertSee('الحجز');
    });

    test('about page returns 200 and renders bio, hours, and location', function () {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('أوقات العمل الرسمية');
        $response->assertSee('موقع الصالون');
        $response->assertSee('لماذا هيرلاين؟');
        $response->assertSee('حي الملقا');
    });

    test('services catalog page returns 200 and lists categories', function () {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('خدمات الشعر');
        $response->assertSee('معالجات الشعر');
        $response->assertSee('جلسات العناية بالشعر');
        $response->assertSee('الاستشوار والتصفيف');
        $response->assertSee('المناكير والبادكير');
        $response->assertSee('البشرة، المكياج والرموش');
        $response->assertSee('المساج، الحلاوة والواكس');
    });

    test('booking page returns 200 and shows all steps', function () {
        $response = $this->get('/booking');

        $response->assertStatus(200);
        $response->assertSee('الخدمة المطلوبة');
        $response->assertSee('نوع الخدمة');
        $response->assertSee('اختاري الخبيرة');
        $response->assertSee('اختاري اليوم والتاريخ');
        $response->assertSee('اختاري الوقت المتاح');
        $response->assertSee('تأكيد الحجز');
    });

    test('profile page returns 200 and shows login form', function () {
        $response = $this->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('تسجيل الدخول');
        $response->assertSee('كود العميلة أو رقم الجوال');
        $response->assertSee('HL-1001');
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
