<?php

declare(strict_types=1);

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Specialist;
use App\Services\FrontData\BookingCalendarDataService;
use App\Services\FrontData\CartAndCheckoutDataService;
use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\CatalogMenuDataService;
use App\Services\FrontData\CustomerAccountDataService;
use App\Services\FrontData\HomeServiceDataService;
use App\Services\FrontData\SalonInfoDataService;
use Illuminate\Support\Facades\Http;

describe('Customer-Facing Pages', function () {
    test('home page returns 200 and renders brand identity', function () {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('HAIRLINE');
        $response->assertSee('هيرلاين');
        $response->assertSee('BEAUTY SALON');
        $response->assertSee('id="fullscreenBtn"', false);
        $response->assertSee('toggleFullscreen()', false);
        $response->assertSee('جميع الخدمات');
        $response->assertSee('الخدمات المتاحة للحجز');
        $response->assertSee('أناقتكِ تتجدد');
        $response->assertSee('عن هيرلاين');
        $response->assertSee('حسابي');
        $response->assertSee('سياسة الخصوصية');
    });

    test('about page returns 200 and renders bio, hours, and location', function () {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('هيرلاين');
        $response->assertSee('HAIRLINE BEAUTY SALON');
        $response->assertSee('من نحن');
        $response->assertSee('أوقات العمل الرسمية');
        $response->assertSee('التعريف بالصالون');
    });

    test('services catalog page returns 200 and renders official 8-page catalog without booking transition button', function () {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('جميع الخدمات (زيارة فورية)');
        $response->assertSee('خدمات الشعر');
        $response->assertSee('معالجات الشعر');
        $response->assertSee('الاستشوار');
        $response->assertSee('المناكير و البادكير');
        $response->assertSee('أطــــوال الشــــعر');
        $response->assertSee('services-menu.css');
        $response->assertSee('servicesTabView');
        $response->assertSee('serviceSearch');
        $response->assertDontSee('الانتقال إلى الخدمات المتاحة للحجز');
    });

    test('booking page returns 200 and shows all steps including confirmation details', function () {
        $response = $this->get('/booking');

        $response->assertStatus(200);
        $response->assertSee('الخدمات المتاحة للحجز');
        $response->assertSee('خدمات الشعر');
        $response->assertSee('معالجات الشعر');
        $response->assertSee('جلسات العناية بالشعر');
        $response->assertSee('الاستشوار والتصفيف');
        $response->assertSee('المناكير والبادكير');
        $response->assertSee('تم تأكيد حجزك بنجاح');
        $response->assertSee('الخدمة المختارة:');
        $response->assertSee('الأخصائية:');
        $response->assertSee('الموعد:');
        $response->assertDontSee('القسم الرئيسي:');
        $response->assertDontSee('التكلفة التقديرية (شاملة VAT):');
        $response->assertDontSee('موعدكِ مؤكد يا جميلة!');
    });

    test('profile page returns 200 and shows member profile and bookings', function () {
        $response = $this->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('أهلاً بكِ، عميلتنا المميزة');
        $response->assertSee('VIP');
        $response->assertSee('عضوية هيرلاين الذهبية');
        $response->assertSee('حجوزاتي المؤكدة');
    });

    test('verifying an existing Saudi phone number immediately confirms and saves booking in database', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $service = Service::firstOrFail();
        $specialist = Specialist::firstOrFail();
        $initialBookingCount = Booking::count();

        $response = $this->postJson(route('booking.verify_phone'), [
            'phone' => '+966551234567',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '05:30 م',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'exists' => true,
                'status' => 'confirmed',
                'customer' => [
                    'id' => $customer->id,
                    'name' => 'سارة العتيبي',
                    'phone' => '0551234567',
                ],
                'booking' => [
                    'customerName' => 'سارة العتيبي',
                    'serviceName' => $service->getTranslation('name', 'ar'),
                ],
            ]);

        expect(Booking::count())->toBe($initialBookingCount + 1);
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'status' => 'confirmed',
        ]);
        $this->assertDatabaseHas('booking_items', [
            'service_id' => $service->id,
            'specialist_id' => $specialist->id,
        ]);
    });

    test('verifying a non-existing Saudi phone number returns needs_registration for Google sign-up', function () {
        $service = Service::firstOrFail();
        $specialist = Specialist::firstOrFail();
        $initialBookingCount = Booking::count();

        $response = $this->postJson(route('booking.verify_phone'), [
            'phone' => '0509988776',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '05:30 م',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'exists' => false,
                'status' => 'needs_registration',
                'phone' => '0509988776',
            ]);

        expect(Booking::count())->toBe($initialBookingCount);
    });

    test('rejects invalid non-Saudi phone number with 422 validation error', function () {
        $service = Service::firstOrFail();
        $specialist = Specialist::firstOrFail();

        $response = $this->postJson(route('booking.verify_phone'), [
            'phone' => '01234567',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
            ]);
    });

    test('registering a new customer via Google saves customer and booking in database with email owner name', function () {
        $service = Service::firstOrFail();
        $specialist = Specialist::firstOrFail();

        $response = $this->postJson(route('booking.google_register'), [
            'phone' => '0509988776',
            'email' => 'maha.alqhtani@gmail.com',
            'name' => 'مها القحطاني',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'الأحد القادم',
            'booking_time' => '08:00 م',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'exists' => true,
                'registered_via_google' => true,
                'status' => 'confirmed',
                'customer' => [
                    'name' => 'مها القحطاني',
                    'phone' => '0509988776',
                    'email' => 'maha.alqhtani@gmail.com',
                ],
                'booking' => [
                    'customerName' => 'مها القحطاني',
                    'serviceName' => $service->getTranslation('name', 'ar'),
                ],
            ]);

        $this->assertDatabaseHas('customers', [
            'phone' => '0509988776',
            'email' => 'maha.alqhtani@gmail.com',
            'name' => 'مها القحطاني',
        ]);

        $newCustomer = Customer::where('phone', '0509988776')->firstOrFail();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $newCustomer->id,
            'status' => 'confirmed',
        ]);
    });

    test('google oauth redirect and callback completes registration and confirms booking with google account owner name', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-access-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'reem.alsaud@gmail.com',
                'name' => 'ريم آل سعود',
                'picture' => 'https://example.com/avatar.jpg',
            ], 200),
        ]);

        $service = Service::firstOrFail();
        $specialist = Specialist::firstOrFail();

        $redirectResponse = $this->get(route('booking.google_redirect', [
            'phone' => '0554433221',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '05:30 م',
        ]));

        $redirectResponse->assertRedirect();
        expect($redirectResponse->headers->get('Location'))->toContain('https://accounts.google.com/o/oauth2/v2/auth');

        $state = base64_encode((string) json_encode([
            'phone' => '0554433221',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '05:30 م',
        ], JSON_UNESCAPED_UNICODE));

        $callbackResponse = $this->get(route('booking.google_callback', [
            'code' => 'mock-auth-code',
            'state' => $state,
        ]));

        $callbackResponse->assertStatus(200);
        $callbackResponse->assertSee('HAIRLINE_GOOGLE_AUTH_RESULT', false);
        $callbackResponse->assertSee('ريم آل سعود');

        $this->assertDatabaseHas('customers', [
            'phone' => '0554433221',
            'email' => 'reem.alsaud@gmail.com',
            'name' => 'ريم آل سعود',
        ]);
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
        expect($location['district'])->toBe('حي المصيف');
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

    test('CatalogMenuDataService returns all 8 official menu pages', function () {
        $service = app(CatalogMenuDataService::class);

        $pages = $service->getMenuPages();
        expect($pages)->toHaveCount(8);
        expect($pages[0]['title_ar'])->toBe('خدمات الشعر');
        expect($pages[1]['title_ar'])->toBe('معالجات الشعر');
        expect($pages[2]['title_ar'])->toBe('جلسات العناية بالشعر');
        expect($pages[3]['title_ar'])->toBe('الاستشوار');
        expect($pages[4]['title_ar'])->toBe('المناكير و البادكير');
        expect($pages[7]['title_ar'])->toBe('أطــــوال الشــــعر');
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
