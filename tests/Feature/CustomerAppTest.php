<?php

declare(strict_types=1);

use App\Exceptions\SlotUnavailableException;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\SpecialistShift;
use App\Services\Booking\BookingCreationService;
use App\Services\Cache\HairlineCacheService;
use App\Services\FrontData\BookingCalendarDataService;
use App\Services\FrontData\CartAndCheckoutDataService;
use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\CatalogMenuDataService;
use App\Services\FrontData\CustomerAccountDataService;
use App\Services\FrontData\HomeServiceDataService;
use App\Services\FrontData\SalonInfoDataService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
        $response->assertSee('هيرلاين لأن الجمال إحساس');
        $response->assertSee('عن هيرلاين');
        $response->assertSee('حسابي');
        $response->assertSee('سياسة الخصوصية');
    });

    test('home page renders logged-in customer avatar and verification bottom-sheet elements', function () {
        $customer = Customer::create([
            'customer_code' => 'CUST-TEST-AVATAR-'.uniqid(),
            'name' => 'Hakim Ahmed',
            'phone' => '0554545450',
            'email' => 'hakim_test_avatar@example.com',
            'avatar_url' => 'https://example.com/avatar.jpg',
            'is_active' => true,
        ]);

        $response = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="loggedInCustomerAvatarDisplay"', false);
        $response->assertSee('id="loggedInCustomerAvatarFallback"', false);
        $response->assertSee('https://example.com/avatar.jpg', false);
    });

    test('about page returns 200 and renders bio, hours, and location', function () {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('هيرلاين');
        $response->assertSee('HAIRLINE BEAUTY SALON');
        $response->assertSee('من نحن');
        $response->assertSee('أوقات العمل الرسمية');
        $response->assertSee('التعريف بالصالون');
        $response->assertSee('id="aboutContactSection"', false);
        $response->assertSee('(للتواصل)');
        $response->assertDontSee('الهاتف والحجوزات');
        $response->assertDontSee('حسابات وروابط التواصل');
        $response->assertSee('العنوان');
        $response->assertDontSee('العنوان وموقع الصالون');
        $response->assertDontSee('تعرفي على الأخصائيات');
        $response->assertDontSee('معلومات التواصل');
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
        $response->assertSee('id="serviceSearch"', false);
        $response->assertSee('id="submitSearchBtn"', false);
        $response->assertSee('id="servicesSearchStatus"', false);
        $response->assertSee('id="servicesSearchEmptyState"', false);
        $response->assertSee('id="servicesSuggestionsDropdown"', false);
        $response->assertSee('id="servicesSuggestionsList"', false);
        $response->assertSee("executeUnifiedServiceSearch('services', event)", false);
        $response->assertDontSee('الانتقال إلى الخدمات المتاحة للحجز');
    });

    test('ajax suggestions endpoint returns up to 10 services ranked character-by-character', function () {
        // 1. Empty query returns empty suggestions
        $emptyResponse = $this->getJson(route('services.suggestions', ['q' => '']));
        $emptyResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 0,
                'suggestions' => [],
            ]);

        // 2. Character-by-character query (e.g. 'ب') returns at most 10 suggestions
        $singleCharResponse = $this->getJson(route('services.suggestions', ['q' => 'ب']));
        $singleCharResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $singleCharSuggestions = $singleCharResponse->json('suggestions');
        expect(count($singleCharSuggestions))->toBeLessThanOrEqual(10);
        expect(count($singleCharSuggestions))->toBeGreaterThan(0);

        // 3. Multi-character query (e.g. 'برو') returns matching services
        $multiCharResponse = $this->getJson(route('services.suggestions', ['q' => 'برو']));
        $multiCharResponse->assertStatus(200);
        $multiCharSuggestions = $multiCharResponse->json('suggestions');
        expect(count($multiCharSuggestions))->toBeLessThanOrEqual(10);
        expect(count($multiCharSuggestions))->toBeGreaterThan(0);
        expect($multiCharSuggestions[0])->toHaveKeys(['id', 'name', 'category', 'price', 'price_display']);
    });

    test('booking page returns 200 and shows standalone collapsible tree view with checkboxes and floating confirm button', function () {
        HairlineCacheService::clearCatalog();

        $response = $this->get('/booking');

        $response->assertStatus(200);
        $response->assertSee('id="bookingTabView"', false);
        $response->assertSee('id="bookingStep1Container"', false);
        $response->assertDontSee('id="bookingServiceSearch"', false);
        $response->assertDontSee('id="submitBookingSearchBtn"', false);
        $response->assertDontSee('id="bookingSearchStatus"', false);
        $response->assertDontSee('id="bookingSearchEmptyState"', false);
        $response->assertDontSee("executeUnifiedServiceSearch('booking', event)", false);
        $response->assertSee('aria-expanded="true"', false);
        $response->assertSee('booking-service-checkbox', false);
        $response->assertSee('data-price-hint', false);
        $response->assertDontSee('1 أخصائيات متاحات');
        $response->assertSee('id="floatingBookingConfirmContainer"', false);
        $response->assertSee('id="floatingBookingConfirmBtn"', false);
        $response->assertSee('تأكيد الحجز واختيار الأخصائية');
        $response->assertSee('الخدمات المتاحة للحجز');
        $response->assertSee('خدمات الشعر');
        $response->assertSee('معالجات الشعر');
        $response->assertSee('البروتين بالتمليس الحراري');
        $response->assertSee('المكياج');
        $response->assertSee('data-category-tree="hair_services"', false);
        $response->assertSee('data-category-tree="hair_treatments"', false);
        $response->assertSee('data-category-tree="skin_eyebrows"', false);
        $response->assertDontSee('data-category-tree="nails"', false);
        $response->assertSee('تم تأكيد حجزك بنجاح');
        $response->assertSee('الخدمة المختارة');
        $response->assertSee('الأخصائية');
        $response->assertSee('الموعد');
        $response->assertDontSee('booking-cat-count', false);
        $response->assertDontSee('bookingCustomerNotesInput', false);
        $response->assertDontSee('القسم الرئيسي:');
        $response->assertDontSee('التكلفة التقديرية (شاملة VAT):');
        $response->assertDontSee('موعدكِ مؤكد يا جميلة!');
    });

    test('profile page returns 200 without fake data and renders edit profile modal', function () {
        $response = $this->get('/profile');

        $response->assertStatus(200);
        $response->assertDontSee('عميلتنا المميزة');
        $response->assertDontSee('عضوية هيرلاين الذهبية');
        $response->assertDontSee('نقطة ولاء');
        $response->assertSee('حجوزاتي المؤكدة');
        $response->assertSee('id="editProfileModal"', false);
    });

    test('specialists page returns 200 and renders standalone specialists grid and interactive detail modal with prev/next navigation', function () {
        $response = $this->get('/specialists');

        $response->assertStatus(200);
        $response->assertSee('id="specialistsTabView"', false);
        $response->assertSee('id="specialistsGridContainer"', false);
        $response->assertSee('كارين');
        $response->assertSee('Karen');
        $response->assertSee('جانيت');
        $response->assertSee('جينكي');
        $response->assertSee('روث');
        $response->assertSee('id="specialist-card-0"', false);
        $response->assertSee('openSpecialistModal(0)', false);
        $response->assertSee('id="specialistDetailModal"', false);
        $response->assertSee('id="specialistModalPrevBtn"', false);
        $response->assertSee('id="specialistModalNextBtn"', false);
        $response->assertSee('navigateSpecialistModal(-1, event)', false);
        $response->assertSee('navigateSpecialistModal(1, event)', false);
        $response->assertSee('closeSpecialistModal()', false);
        $response->assertDontSee('الأخصائيات المعتمدات في هيرلاين');
        $response->assertDontSee('أبرز الأوقات المتاحة');
        $response->assertDontSee('احجزي موعداً مع الأخصائية');
        $response->assertDontSee('أخصائية معتمدة في هيرلاين');
    });

    test('authenticated customer can update name, phone, and avatar in profile popup', function () {
        Storage::fake('public');

        $customer = Customer::where('phone', '0551234567')->firstOrFail();

        $profilePage = $this->withSession(['logged_in_customer_id' => $customer->id])->get('/profile');
        $profilePage->assertStatus(200)
            ->assertSee('سارة العتيبي')
            ->assertSee('تعديل الملف');

        $avatarFile = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $updateResponse = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('profile.update'), [
                'name' => 'سارة آل عتيبي',
                'phone' => '+966559998887',
                'avatar' => $avatarFile,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => 'سارة آل عتيبي',
                    'phone' => '0559998887',
                ],
            ]);

        $customer->refresh();
        expect($customer->name)->toBe('سارة آل عتيبي');
        expect($customer->phone)->toBe('0559998887');
        expect($customer->avatar_url)->toContain('storage/customers/');
    });

    test('verifying an existing Saudi phone number returns customer_found with customer name for guest flow', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $initialBookingCount = Booking::count();

        $response = $this->postJson(route('booking.verify_phone'), [
            'phone' => '+966551234567',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '04:00 م',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'customer_found',
                'customer_name' => $customer->name,
                'phone' => '0551234567',
            ]);

        expect(Booking::count())->toBe($initialBookingCount);
        $this->assertNull(session('logged_in_customer_id'));
    });

    test('active authenticated customer can confirm booking immediately via verify_phone', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $initialBookingCount = Booking::count();

        $response = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => 'غداً - السبت',
                'booking_time' => '04:00 م',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'exists' => true,
                'status' => 'confirmed',
                'customer' => [
                    'id' => $customer->id,
                    'name' => 'سارة العتيبي',
                ],
            ]);

        expect(Booking::count())->toBe($initialBookingCount + 1);
    });

    test('verifying a non-existing Saudi phone number returns new_customer status requesting name', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $initialBookingCount = Booking::count();

        $response = $this->postJson(route('booking.verify_phone'), [
            'phone' => '0509988776',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '04:00 م',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'new_customer',
                'phone' => '0509988776',
            ]);

        expect(Booking::count())->toBe($initialBookingCount);
    });

    test('rejects invalid non-Saudi phone number with 422 validation error', function () {
        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();

        $response = $this->postJson(route('booking.verify_phone'), [
            'phone' => '01234567',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '05:30 م',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
            ]);
    });

    test('registering a new customer via Google requires oauth code and binds verified email', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-access-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'maha.alqhtani@gmail.com',
                'email_verified' => true,
                'name' => 'مها القحطاني',
                'picture' => 'https://example.com/maha.jpg',
            ], 200),
        ]);

        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();

        $response = $this->postJson(route('booking.google_register'), [
            'code' => 'valid-mock-code',
            'phone' => '0509988776',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'الأحد القادم',
            'booking_time' => '02:00 م',
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
            'status' => Booking::STATUS_PENDING,
        ]);
    });

    test('google oauth redirect and callback completes registration and confirms booking with google account owner name', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-access-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'reem.alsaud@gmail.com',
                'email_verified' => true,
                'name' => 'ريم آل سعود',
                'picture' => 'https://example.com/avatar.jpg',
            ], 200),
        ]);

        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();

        $redirectResponse = $this->get(route('booking.google_redirect', [
            'phone' => '0554433221',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => 'غداً - السبت',
            'booking_time' => '04:00 م',
        ]));

        $redirectResponse->assertRedirect();
        expect($redirectResponse->headers->get('Location'))->toContain('https://accounts.google.com/o/oauth2/v2/auth');

        $state = session('google_oauth_state');

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

    test('guest booking confirms booking for new customer, creates customer record, logs in, and sets customer_type to guest', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(4)->toDateString();

        $response = $this->postJson(route('booking.guest_confirm'), [
            'name' => 'نورة محمد',
            'phone' => '0501112233',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
                'customer_type' => Booking::CUSTOMER_TYPE_GUEST,
                'customer' => [
                    'name' => 'نورة محمد',
                    'phone' => '0501112233',
                ],
            ]);

        $customer = Customer::where('phone', '0501112233')->firstOrFail();
        expect($customer->name)->toBe('نورة محمد');
        expect(session('logged_in_customer_id'))->toBe($customer->id);

        $booking = Booking::where('customer_id', $customer->id)->latest('id')->firstOrFail();
        expect($booking->customer_type)->toBe(Booking::CUSTOMER_TYPE_GUEST);
        expect($booking->isGuest())->toBeTrue();
        expect($booking->isRegistered())->toBeFalse();
    });

    test('guest booking supports multi-service selection creating multiple booking items', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $services = $specialist->services()->take(2)->get();
        if ($services->count() < 2) {
            $anotherService = Service::firstOrFail();
            $specialist->services()->syncWithoutDetaching([$anotherService->id]);
            $services = $specialist->services()->take(2)->get();
        }
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(5)->toDateString();
        $serviceCodes = $services->pluck('code')->all();

        $response = $this->postJson(route('booking.guest_confirm'), [
            'name' => 'عبير الخالد',
            'phone' => '0502223344',
            'service_id' => $serviceCodes,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
                'customer_type' => Booking::CUSTOMER_TYPE_GUEST,
            ]);

        $customer = Customer::where('phone', '0502223344')->firstOrFail();
        $booking = Booking::where('customer_id', $customer->id)->latest('id')->firstOrFail();
        expect($booking->items()->count())->toBe(count($serviceCodes));
    });

    test('guest booking records guests_count and customer_notes properly', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(6)->toDateString();

        $response = $this->postJson(route('booking.guest_confirm'), [
            'name' => 'سارة العتيبي',
            'phone' => '0503334455',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
            'guests_count' => 3,
            'customer_notes' => 'نود الجلوس سوياً إن أمكن، وشعري بحاجة لعناية خاصة',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
                'booking' => [
                    'guests_count' => 3,
                    'customer_notes' => 'نود الجلوس سوياً إن أمكن، وشعري بحاجة لعناية خاصة',
                ],
            ]);

        $booking = Booking::where('customer_notes', 'like', '%نود الجلوس سوياً%')->firstOrFail();
        expect($booking->guests_count)->toBe(3);
        expect($booking->customer_notes)->toBe('نود الجلوس سوياً إن أمكن، وشعري بحاجة لعناية خاصة');
    });

    test('booking guest confirm rejects invalid guests_count', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(6)->toDateString();

        $response = $this->postJson(route('booking.guest_confirm'), [
            'name' => 'سارة العتيبي',
            'phone' => '0503334456',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
            'guests_count' => 0,
        ]);

        $response->assertStatus(422);
    });

    test('dedicated contact and privacy pages return 200 with appropriate content', function () {
        $contactResponse = $this->get('/contact');
        $contactResponse->assertStatus(200);
        $contactResponse->assertSee('للتواصل والاستفسار');
        $contactResponse->assertSee('أوقات العمل الرسمية');

        $privacyResponse = $this->get('/privacy');
        $privacyResponse->assertStatus(200);
        $privacyResponse->assertSee('سياسة الخصوصية');
        $privacyResponse->assertSee('حماية وسرية البيانات الشخصية');
    });

    test('booking and contact page visual updates: hidden service counter, tabda min, no notes, original social brand colors, and hidden fab on profile', function () {
        // 1. Booking page checks
        $bookingResponse = $this->get('/booking');
        $bookingResponse->assertStatus(200);
        $bookingResponse->assertDontSee('booking-cat-count', false);
        $bookingResponse->assertDontSee('bookingCustomerNotesInput', false);
        $bookingResponse->assertSee('تبدأ من');
        $bookingResponse->assertDontSee('يبدأ من 350');

        // 2. Contact page checks (original brand colors)
        $contactResponse = $this->get('/contact');
        $contactResponse->assertStatus(200);
        $contactResponse->assertSee('bg-[#25D366]', false);
        $contactResponse->assertSee('bg-gradient-to-tr from-[#f09433] via-[#dc2743] to-[#bc1888]', false);
        $contactResponse->assertSee('bg-[#FFFC00]', false);
        $contactResponse->assertSee('bg-black text-white', false);

        // 3. Profile & my-bookings page checks:
        // Guest (not logged in): floating booking button remains visible on desktop on profile page
        $guestProfileResponse = $this->get('/profile');
        $guestProfileResponse->assertStatus(200);
        $guestProfileResponse->assertSee('id="desktopFloatingBookingContainer" class="hidden md:flex', false);
        $guestProfileResponse->assertDontSee('id="desktopFloatingBookingContainer" class="hidden md:hidden', false);

        // Authenticated customer (logged in): floating booking concierge is hidden on profile / account page
        $customer = Customer::create([
            'customer_code' => 'CUST-TEST-FAB-'.uniqid(),
            'name' => 'نوف السعيد',
            'phone' => '0501112233',
            'is_active' => true,
        ]);

        $authProfileResponse = $this->withSession(['logged_in_customer_id' => $customer->id])->get('/profile');
        $authProfileResponse->assertStatus(200);
        $authProfileResponse->assertSee('id="desktopFloatingBookingContainer" class="hidden md:hidden', false);
        $authProfileResponse->assertSee('style="display: none;"', false);

        $authMyBookingsResponse = $this->withSession(['logged_in_customer_id' => $customer->id])->get('/my-bookings');
        $authMyBookingsResponse->assertStatus(200);
        $authMyBookingsResponse->assertSee('id="desktopFloatingBookingContainer" class="hidden md:hidden', false);
        $authMyBookingsResponse->assertSee('style="display: none;"', false);
    });

    test('sidebar drawer navigation displays all main sections on mobile and contact privacy on desktop', function () {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('id="sidebarDrawer"', false);
        $response->assertSee('id="menuBtn"', false);
        $response->assertDontSee('id="menuBtn"'."\n".'                                onclick="toggleSidebar(true)"'."\n".'                                title="القائمة الجانبية"'."\n".'                                aria-label="القائمة الجانبية"'."\n".'                                class="header-action-btn md:hidden', false);
        $response->assertSee('openSidebarSection(\'about\')', false);
        $response->assertSee('openSidebarSection(\'services\')', false);
        $response->assertSee('openSidebarSection(\'booking\')', false);
        $response->assertSee('openSidebarSection(\'specialists\')', false);
        $response->assertSee('openSidebarSection(\'contact\')', false);
        $response->assertSee('openSidebarSection(\'privacy\')', false);
        $response->assertSee('عن هيرلاين');
        $response->assertSee('الخدمات');
        $response->assertSee('دليل الخدمات');
        $response->assertSee('الخدمات المتاحة للحجز');
        $response->assertSee('الاخصائيات');
        $response->assertSee('للتواصل والاستفسار');
        $response->assertSee('للتواصل');
        $response->assertSee('سياسة الخصوصية');
        $response->assertDontSee('القائمة التعريفية');
        $response->assertDontSee('القائمة الرئيسية');
    });

    test('desktop horizontal navigation displays items in requested order', function () {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();
        $homePos = strpos($content, 'id="desktop-nav-home"');
        $aboutPos = strpos($content, 'id="desktop-nav-about"');
        $servicesPos = strpos($content, 'id="desktop-nav-services"');
        $specialistsPos = strpos($content, 'id="desktop-nav-specialists"');
        $accountPos = strpos($content, 'id="desktop-nav-account"');

        expect($homePos)->not->toBeFalse()
            ->and($aboutPos)->not->toBeFalse()
            ->and($servicesPos)->not->toBeFalse()
            ->and($specialistsPos)->not->toBeFalse()
            ->and($accountPos)->not->toBeFalse();

        expect($homePos)->toBeLessThan($aboutPos)
            ->and($aboutPos)->toBeLessThan($servicesPos)
            ->and($servicesPos)->toBeLessThan($specialistsPos)
            ->and($specialistsPos)->toBeLessThan($accountPos);
    });

    test('availability endpoint generates time slots from database specialist shifts and marks booked slots', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(3);

        SpecialistShift::updateOrCreate(
            [
                'specialist_id' => $specialist->id,
                'day_of_week' => (int) $targetDate->dayOfWeek,
            ],
            [
                'start_time' => '15:00:00',
                'end_time' => '21:00:00',
                'is_day_off' => false,
            ]
        );

        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => $targetDate->toDateString(),
                'booking_time' => '03:00 م',
            ])->assertStatus(200);

        $response = $this->getJson(route('booking.availability', [
            'specialist_id' => $specialist->slug,
            'service_id' => $service->code,
            'booking_date' => $targetDate->toDateString(),
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'specialist_id' => 'sp_janet',
                'date_iso' => $targetDate->toDateString(),
                'is_day_off' => false,
                'shift_start' => '15:00:00',
                'shift_end' => '21:00:00',
            ]);

        $slots = collect($response->json('slots'));
        $firstSlot = $slots->firstWhere('time', '03:00 م');

        expect($firstSlot)->not->toBeNull()
            ->and($firstSlot['is_booked'])->toBeTrue()
            ->and($firstSlot['is_available'])->toBeFalse();
    });

    test('WithinSpecialistShift rule rejects bookings outside shift hours or on specialist day off', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(4);

        // 1. Outside shift hours (10:00 AM is before 14:00 PM shift start)
        $outsideResponse = $this->postJson(route('booking.verify_phone'), [
            'phone' => '0551234567',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate->toDateString(),
            'booking_time' => '10:00 ص',
        ]);

        $outsideResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
            ]);

        // 2. Specialist day off (is_day_off = true)
        SpecialistShift::updateOrCreate(
            [
                'specialist_id' => $specialist->id,
                'day_of_week' => (int) $targetDate->dayOfWeek,
            ],
            [
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'is_day_off' => true,
            ]
        );

        $dayOffResponse = $this->postJson(route('booking.verify_phone'), [
            'phone' => '0551234567',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate->toDateString(),
            'booking_time' => '05:30 م',
        ]);

        $dayOffResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'الأخصائية المختارة في يوم إجازة في هذا التاريخ ولا توجد فترات عمل متاحة.',
            ]);
    });

    test('AvailableSpecialistSlot rule prevents double-booking an already booked specialist time slot', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $otherSpecialist = Specialist::where('slug', 'sp_karen')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $otherSpecialist->services()->syncWithoutDetaching([$service->id]);
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(5)->toDateString();

        $customer = Customer::where('phone', '0551234567')->firstOrFail();

        // First booking succeeds
        $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => $targetDate,
                'booking_time' => '05:00 م',
            ])->assertStatus(200);

        // Second overlapping booking for the SAME specialist on the SAME date & time fails with 422
        $conflictResponse = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => $targetDate,
                'booking_time' => '05:00 م',
            ]);

        $conflictResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'عذراً، هذا الموعد محجوز مسبقاً مع الأخصائية المختارة. يرجى اختيار وقت آخر متاح.',
            ]);

        // Booking the same time slot with a DIFFERENT specialist succeeds
        $otherSpecialistResponse = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $otherSpecialist->slug,
                'booking_date' => $targetDate,
                'booking_time' => '05:00 م',
            ]);

        $otherSpecialistResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
            ]);
    });

    test('google registration fails when google email does not match registered customer phone email', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'attacker@gmail.com',
                'email_verified' => true,
                'name' => 'Attacker',
            ], 200),
        ]);

        // Phone 0551234567 belongs to sara.otb@example.com
        $response = $this->postJson(route('booking.google_register'), [
            'code' => 'mock-code',
            'phone' => '0551234567',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'حساب Google لا يطابق البريد الإلكتروني المسجل لهذا الرقم.',
            ]);
    });

    test('registerWithGoogle rejects requests missing oauth code and refuses spoofed request emails', function () {
        $response = $this->postJson(route('booking.google_register'), [
            'phone' => '0501122334',
            'email' => 'spoofed@example.com',
            'name' => 'Spoofed User',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'رمز التحقق من حساب Google مطلوب لإتمام العملية.',
            ]);
    });

    test('registerWithGoogle rejects unverified google email addresses', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'unverified@gmail.com',
                'email_verified' => false,
                'name' => 'Unverified User',
            ], 200),
        ]);

        $response = $this->postJson(route('booking.google_register'), [
            'code' => 'mock-code',
            'phone' => '0501122334',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'validation_error',
            ]);
    });

    test('direct Google sign in from profile logs in existing customer without phone number', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'sara.otb@example.com',
                'email_verified' => true,
                'name' => 'سارة العتيبي',
            ], 200),
        ]);

        $response = $this->postJson(route('booking.google_register'), [
            'code' => 'valid-oauth-code',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
                'registered_via_google' => true,
                'customer' => [
                    'name' => 'سارة العتيبي',
                    'email' => 'sara.otb@example.com',
                    'phone' => '0551234567',
                ],
            ]);

        $sara = Customer::where('email', 'sara.otb@example.com')->firstOrFail();
        expect(session('logged_in_customer_id'))->toBe($sara->id);
    });

    test('direct Google sign in from profile registers new customer without phone number', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'direct.profile.user@gmail.com',
                'email_verified' => true,
                'name' => 'هدى الحربي',
                'picture' => 'https://example.com/huda.jpg',
            ], 200),
        ]);

        $response = $this->postJson(route('booking.google_register'), [
            'code' => 'valid-oauth-code',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
                'registered_via_google' => true,
                'customer' => [
                    'name' => 'هدى الحربي',
                    'email' => 'direct.profile.user@gmail.com',
                    'phone' => null,
                ],
            ]);

        $newCustomer = Customer::where('email', 'direct.profile.user@gmail.com')->firstOrFail();
        expect($newCustomer->phone)->toBeNull();
        expect(session('logged_in_customer_id'))->toBe($newCustomer->id);
    });

    test('direct Google sign in from profile with is_profile flag registers customer without requiring phone even if service_id was in payload', function () {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'profile.only.google@gmail.com',
                'email_verified' => true,
                'name' => 'نوف السبيعي',
                'picture' => 'https://example.com/nouf.jpg',
            ], 200),
        ]);

        $response = $this->postJson(route('booking.google_register'), [
            'code' => 'valid-oauth-code',
            'is_profile' => true,
            'service_id' => 'leftover-service-id',
            'specialist_id' => 'leftover-specialist-id',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
                'registered_via_google' => true,
                'customer' => [
                    'name' => 'نوف السبيعي',
                    'email' => 'profile.only.google@gmail.com',
                    'phone' => null,
                ],
            ]);

        $newCustomer = Customer::where('email', 'profile.only.google@gmail.com')->firstOrFail();
        expect($newCustomer->phone)->toBeNull();
        expect(session('logged_in_customer_id'))->toBe($newCustomer->id);
    });

    test('profile update requires authenticated session and rejects customer_id tampering', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();

        $response = $this->postJson(route('profile.update'), [
            'customer_id' => $customer->id,
            'name' => 'مخترق',
            'phone' => '0559999999',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status' => 'unauthenticated',
            ]);

        $customer->refresh();
        expect($customer->name)->toBe('سارة العتيبي');
    });

    test('booking creation handles concurrency race condition and throws SlotUnavailableException', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(6)->toDateString();

        $creationService = app(BookingCreationService::class);
        $createdBooking = $creationService->createBookingForCustomer(
            customer: $customer,
            serviceCode: $service->code,
            specialistSlug: $specialist->slug,
            dateLabel: $targetDate,
            timeSlot: '04:00 م'
        );

        expect($createdBooking['id'])->toMatch('/^\d{4}$/');

        expect(fn () => $creationService->createBookingForCustomer(
            customer: $customer,
            serviceCode: $service->code,
            specialistSlug: $specialist->slug,
            dateLabel: $targetDate,
            timeSlot: '04:00 م'
        ))->toThrow(SlotUnavailableException::class);
    });

    test('booking rejects missing booking date or time without applying hidden defaults', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();

        // Missing booking_date
        $responseNoDate = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_time' => '05:30 م',
            ]);

        $responseNoDate->assertStatus(422)
            ->assertJsonValidationErrors(['booking_date']);

        // Missing booking_time
        $responseNoTime = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => 'غداً - السبت',
            ]);

        $responseNoTime->assertStatus(422)
            ->assertJsonValidationErrors(['booking_time']);
    });

    test('booking rejects mismatched phone number when customer is already logged in', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(5)->toDateString();

        $response = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0598765432',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => $targetDate,
                'booking_time' => '05:00 م',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone'])
            ->assertJsonFragment([
                'phone' => ['رقم الجوال المدخل لا يطابق رقم حسابكِ المسجل حالياً.'],
            ]);
    });

    test('booking rejects specialist when specialist does not offer the selected service', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::firstOrFail();
        $unofferedService = Service::whereDoesntHave('specialists', function ($query) use ($specialist): void {
            $query->where('specialists.id', $specialist->id);
        })->firstOrFail();

        $targetDate = Carbon::today('Asia/Riyadh')->addDays(5)->toDateString();

        $response = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $unofferedService->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => $targetDate,
                'booking_time' => '05:00 م',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['specialist_id'])
            ->assertJsonFragment([
                'specialist_id' => ['عذراً، الأخصائية المختارة لا تقدم هذه الخدمة.'],
            ]);
    });

    test('booking rejects booking dates in the past', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $yesterday = Carbon::yesterday('Asia/Riyadh')->toDateString();

        $response = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => $yesterday,
                'booking_time' => '05:00 م',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['booking_date'])
            ->assertJsonFragment([
                'booking_date' => ['لا يمكن حجز موعد في تاريخ سابق.'],
            ]);
    });

    test('booking rejects past time slots for today', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();

        // Freeze time at 17:00 (5:00 PM) Riyadh time on a Monday
        Carbon::setTestNow(Carbon::parse('2026-11-02 17:00:00', 'Asia/Riyadh'));

        // Attempting to book 02:00 PM (14:00) which has already passed today
        $response = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => '2026-11-02',
                'booking_time' => '02:00 م',
            ]);

        Carbon::setTestNow(); // Reset test clock

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['booking_time'])
            ->assertJsonFragment([
                'booking_time' => ['عذراً، هذا الموعد قد مضى وقته لليوم. يرجى اختيار موعد قادم.'],
            ]);
    });

    test('booking rejects time slot when service duration exceeds specialist shift end time', function () {
        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(3);

        // Ensure specialist shift ends at 22:00:00 on this day
        SpecialistShift::updateOrCreate(
            [
                'specialist_id' => $specialist->id,
                'day_of_week' => (int) $targetDate->dayOfWeek,
            ],
            [
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'is_day_off' => false,
            ]
        );

        // Set service duration to 90 minutes
        $service->update(['duration_minutes' => 90]);

        // Attempt to book at 21:30 (09:30 PM), which ends at 23:00 (exceeds 22:00 shift end)
        $response = $this->withSession(['logged_in_customer_id' => $customer->id])
            ->postJson(route('booking.verify_phone'), [
                'phone' => '0551234567',
                'service_id' => $service->code,
                'specialist_id' => $specialist->slug,
                'booking_date' => $targetDate->toDateString(),
                'booking_time' => '09:30 م',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['booking_time']);

        $errorText = collect($response->json('errors.booking_time'))->first();
        expect($errorText)->toContain('مدة الخدمة تتجاوز وقت انتهاء شيفت الأخصائية');
    });

    test('multi-service booking allows selecting different specialists per service and creates sequential booking items', function () {
        $spKaren = Specialist::where('slug', 'sp_karen')->firstOrFail();
        $spNajla = Specialist::where('slug', 'sp_najla')->firstOrFail();
        $srvHair = Service::where('code', 'hs_all_hairstyles')->firstOrFail();
        $srvMakeup = Service::where('code', 'sk_makeup_lashes')->firstOrFail();

        $targetDate = Carbon::today('Asia/Riyadh')->addDays(4);

        SpecialistShift::updateOrCreate(
            ['specialist_id' => $spKaren->id, 'day_of_week' => (int) $targetDate->dayOfWeek],
            ['start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_day_off' => false]
        );
        SpecialistShift::updateOrCreate(
            ['specialist_id' => $spNajla->id, 'day_of_week' => (int) $targetDate->dayOfWeek],
            ['start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_day_off' => false]
        );

        $srvHair->update(['duration_minutes' => 60]);
        $srvMakeup->update(['duration_minutes' => 45]);
        DB::table('specialist_service')
            ->where('specialist_id', $spNajla->id)
            ->where('service_id', $srvMakeup->id)
            ->update(['custom_duration_minutes' => 45]);

        // 1. Availability query with multi-specialist items
        $availabilityResponse = $this->getJson(route('booking.availability', [
            'booking_date' => $targetDate->toDateString(),
            'items' => json_encode([
                ['service_id' => $srvHair->code, 'specialist_id' => $spKaren->slug],
                ['service_id' => $srvMakeup->code, 'specialist_id' => $spNajla->slug],
            ]),
        ]));

        $availabilityResponse->assertStatus(200)
            ->assertJsonPath('success', true);
        expect($availabilityResponse->json('slots'))->not->toBeEmpty();

        // 2. Booking creation via guest confirm with separate specialists
        $guestBookingResponse = $this->postJson(route('booking.guest_confirm'), [
            'phone' => '0599887766',
            'name' => 'نورة محمد',
            'booking_date' => $targetDate->toDateString(),
            'booking_time' => '04:00 م',
            'items' => [
                ['service_id' => $srvHair->code, 'specialist_id' => $spKaren->slug],
                ['service_id' => $srvMakeup->code, 'specialist_id' => $spNajla->slug],
            ],
        ]);

        $guestBookingResponse->assertStatus(200)
            ->assertJsonPath('status', 'confirmed');

        $bookingDbId = $guestBookingResponse->json('booking.db_id');
        $createdBooking = Booking::with('items')->findOrFail($bookingDbId);

        expect($createdBooking->items)->toHaveCount(2);

        $itemHair = $createdBooking->items->firstWhere('service_id', $srvHair->id);
        $itemMakeup = $createdBooking->items->firstWhere('service_id', $srvMakeup->id);

        expect($itemHair->specialist_id)->toBe($spKaren->id);
        expect($itemHair->scheduled_start_time)->toBe('16:00:00');
        expect($itemHair->scheduled_end_time)->toBe('17:00:00');

        expect($itemMakeup->specialist_id)->toBe($spNajla->id);
        expect($itemMakeup->scheduled_start_time)->toBe('17:00:00');
        expect($itemMakeup->scheduled_end_time)->toBe('17:45:00');

        // 3. Validation rejection if a specialist does not offer the assigned service
        $invalidSpecialistResponse = $this->postJson(route('booking.guest_confirm'), [
            'phone' => '0599887766',
            'name' => 'نورة محمد',
            'booking_date' => $targetDate->toDateString(),
            'booking_time' => '04:00 م',
            'items' => [
                ['service_id' => $srvMakeup->code, 'specialist_id' => $spKaren->slug],
            ],
        ]);

        $invalidSpecialistResponse->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.specialist_id']);
    });

    test('multi-service booking detects collision on second specialist slot and rejects booking', function () {
        $spKaren = Specialist::where('slug', 'sp_karen')->firstOrFail();
        $spNajla = Specialist::where('slug', 'sp_najla')->firstOrFail();
        $srvHair = Service::where('code', 'hs_all_hairstyles')->firstOrFail();
        $srvMakeup = Service::where('code', 'sk_makeup_lashes')->firstOrFail();

        $targetDate = Carbon::today('Asia/Riyadh')->addDays(5);

        SpecialistShift::updateOrCreate(
            ['specialist_id' => $spKaren->id, 'day_of_week' => (int) $targetDate->dayOfWeek],
            ['start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_day_off' => false]
        );
        SpecialistShift::updateOrCreate(
            ['specialist_id' => $spNajla->id, 'day_of_week' => (int) $targetDate->dayOfWeek],
            ['start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_day_off' => false]
        );

        $srvHair->update(['duration_minutes' => 60]);
        $srvMakeup->update(['duration_minutes' => 60]);

        // Create an existing booking for Najla that occupies 17:00 to 18:00
        $existingCustomer = Customer::where('phone', '0551234567')->firstOrFail();
        $existingBooking = Booking::create([
            'booking_number' => '9991',
            'customer_id' => $existingCustomer->id,
            'customer_type' => Booking::CUSTOMER_TYPE_REGISTERED,
            'booking_type' => Booking::BOOKING_TYPE_SALON,
            'booking_date' => $targetDate->toDateString(),
            'overall_start_time' => '17:00:00',
            'overall_end_time' => '18:00:00',
            'status' => Booking::STATUS_CONFIRMED,
            'total_price' => 150,
            'payment_status' => Booking::PAYMENT_STATUS_UNPAID,
        ]);
        BookingItem::create([
            'booking_id' => $existingBooking->id,
            'service_id' => $srvMakeup->id,
            'specialist_id' => $spNajla->id,
            'unit_price' => 150,
            'total_price' => 150,
            'duration_minutes' => 60,
            'scheduled_start_time' => '17:00:00',
            'scheduled_end_time' => '18:00:00',
        ]);

        // Customer attempts to start at 16:00 (04:00 م):
        // Hair with Karen: 16:00 to 17:00 (available)
        // Makeup with Najla: 17:00 to 18:00 (COLLISION with existing booking!)
        $response = $this->postJson(route('booking.guest_confirm'), [
            'phone' => '0599112233',
            'name' => 'سارة العتيبي',
            'booking_date' => $targetDate->toDateString(),
            'booking_time' => '04:00 م',
            'items' => [
                ['service_id' => $srvHair->code, 'specialist_id' => $spKaren->slug],
                ['service_id' => $srvMakeup->code, 'specialist_id' => $spNajla->slug],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['booking_time']);

        $errorText = collect($response->json('errors.booking_time'))->first();
        expect($errorText)->toContain('هذا الموعد محجوز مسبقاً مع الأخصائية المختارة');
    });

    test('multi-service booking allows each specialist to have her own distinct appointment time when selected individually', function () {
        $spKaren = Specialist::where('slug', 'sp_karen')->firstOrFail();
        $spNajla = Specialist::where('slug', 'sp_najla')->firstOrFail();
        $srvHair = Service::where('code', 'hs_all_hairstyles')->firstOrFail();
        $srvMakeup = Service::where('code', 'sk_makeup_lashes')->firstOrFail();

        $targetDate = Carbon::today('Asia/Riyadh')->addDays(6);

        SpecialistShift::updateOrCreate(
            ['specialist_id' => $spKaren->id, 'day_of_week' => (int) $targetDate->dayOfWeek],
            ['start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_day_off' => false]
        );
        SpecialistShift::updateOrCreate(
            ['specialist_id' => $spNajla->id, 'day_of_week' => (int) $targetDate->dayOfWeek],
            ['start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_day_off' => false]
        );

        $srvHair->update(['duration_minutes' => 60]);
        $srvMakeup->update(['duration_minutes' => 45]);

        // Customer books Hair with Karen at 04:00 PM (16:00)
        // and Makeup with Najla at 06:30 PM (18:30) separately
        $response = $this->postJson(route('booking.guest_confirm'), [
            'phone' => '0588776655',
            'name' => 'ريم فهد',
            'booking_date' => $targetDate->toDateString(),
            'booking_time' => '04:00 م',
            'items' => [
                [
                    'service_id' => $srvHair->code,
                    'specialist_id' => $spKaren->slug,
                    'booking_time' => '04:00 م',
                ],
                [
                    'service_id' => $srvMakeup->code,
                    'specialist_id' => $spNajla->slug,
                    'booking_time' => '06:30 م',
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
            ]);

        $bookingDbId = $response->json('booking.db_id');
        $createdBooking = Booking::with('items')->findOrFail($bookingDbId);

        expect($createdBooking->items)->toHaveCount(2);

        $itemHair = $createdBooking->items->firstWhere('service_id', $srvHair->id);
        $itemMakeup = $createdBooking->items->firstWhere('service_id', $srvMakeup->id);

        expect($itemHair->specialist_id)->toBe($spKaren->id);
        expect($itemHair->scheduled_start_time)->toBe('16:00:00');
        expect($itemHair->scheduled_end_time)->toBe('17:00:00');

        expect($itemMakeup->specialist_id)->toBe($spNajla->id);
        expect($itemMakeup->scheduled_start_time)->toBe('18:30:00');
        expect($itemMakeup->scheduled_end_time)->toBe('19:30:00');

        expect($createdBooking->overall_start_time)->toBe('16:00:00');
        expect($createdBooking->overall_end_time)->toBe('19:30:00');
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

        // Unified Arabic & numeral normalization (hamza, teh marbuta, multi-word tokens, Eastern digits)
        $normalizedResults = $service->searchServices('إستشوار قصير');
        expect($normalizedResults)->not->toBeEmpty();

        $tehMarbutaResults = $service->searchServices('صبغه جذور');
        expect($tehMarbutaResults)->not->toBeEmpty();

        $numeralResults = $service->searchServices('١١٠');
        expect($numeralResults)->not->toBeEmpty();

        $totals = $service->calculateTotals(['hs_full_cut', 'nl_manicure']);
        expect($totals['total_price'])->toBe(150);
        expect($totals['count'])->toBe(2);

        // Bookable services depend on explicit is_bookable = true from catalog_seed_data.json
        HairlineCacheService::clearCatalog();
        $bookableCategories = $service->getBookableCategoriesWithSpecialists();
        expect($bookableCategories)->toHaveCount(3);

        expect($service->isServiceBookable('hs_full_color'))->toBeTrue();
        expect($service->isServiceBookable('ht_protein_machine'))->toBeTrue();
        expect($service->isServiceBookable('ht_morphsis_filler'))->toBeTrue();
        expect($service->isServiceBookable('hs_all_hairstyles'))->toBeTrue();
        expect($service->isServiceBookable('sk_makeup_lashes'))->toBeTrue();
        expect($service->isServiceBookable('hs_full_cut'))->toBeFalse();
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

    test('Booking model has status constants and new bookings default to pending with manual whatsapp button', function () {
        expect(Booking::STATUS_PENDING)->toBe('pending');
        expect(Booking::STATUS_CONFIRMED)->toBe('confirmed');
        expect(Booking::STATUS_IN_PROGRESS)->toBe('in_progress');
        expect(Booking::STATUS_COMPLETED)->toBe('completed');
        expect(Booking::STATUS_CANCELLED)->toBe('cancelled');
        expect(Booking::STATUS_NO_SHOW)->toBe('no_show');
        expect(Booking::PAYMENT_STATUS_UNPAID)->toBe('unpaid');
        expect(Booking::getStatuses())->toContain(Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED);

        $customer = Customer::where('phone', '0551234567')->firstOrFail();
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();

        $creationService = app(BookingCreationService::class);
        $payload = $creationService->createBookingForCustomer(
            customer: $customer,
            serviceCode: $service->code,
            specialistSlug: $specialist->slug,
            dateLabel: 'غداً - السبت',
            timeSlot: '04:00 م',
            customerType: Booking::CUSTOMER_TYPE_REGISTERED
        );

        $createdBooking = Booking::findOrFail($payload['db_id']);
        expect($createdBooking->status)->toBe(Booking::STATUS_PENDING);
        expect($createdBooking->isPending())->toBeTrue();
        expect($createdBooking->isConfirmed())->toBeFalse();

        // Verify the booking page does NOT render the manual WhatsApp share button in Step 4
        $bookingPageResponse = $this->get('/booking');
        $bookingPageResponse->assertStatus(200);
        $bookingPageResponse->assertDontSee('id="bookingReceiptStaticWhatsAppBtn"', false);
        $bookingPageResponse->assertDontSee('إرسال تفاصيل الحجز إلى واتساب الصالون');
    });
});
