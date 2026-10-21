<?php

declare(strict_types=1);

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\CatalogPage;
use App\Models\Customer;
use App\Models\District;
use App\Models\FounderStory;
use App\Models\HairLength;
use App\Models\HeroSlide;
use App\Models\MembershipTier;
use App\Models\SalonGallery;
use App\Models\SalonSetting;
use App\Models\SalonWorkingHour;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServicePricingTier;
use App\Models\Specialist;
use App\Services\Cache\HairlineCacheService;
use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\SalonInfoDataService;
use App\Services\FrontData\SpecialistsDataService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

describe('Database Models & Seeded Records', function () {
    test('database contains all seeded salon and catalog entities', function () {
        expect(SalonSetting::count())->toBeGreaterThanOrEqual(1);
        expect(HeroSlide::count())->toBeGreaterThanOrEqual(4);
        expect(SalonGallery::count())->toBeGreaterThanOrEqual(5);
        expect(FounderStory::count())->toBeGreaterThanOrEqual(1);
        expect(SalonWorkingHour::count())->toBe(7);

        expect(CatalogPage::count())->toBe(8);
        expect(ServiceCategory::count())->toBe(7);
        expect(HairLength::count())->toBe(5);
        expect(Service::count())->toBeGreaterThanOrEqual(100);
        expect(ServicePricingTier::count())->toBeGreaterThanOrEqual(50);

        expect(Specialist::count())->toBeGreaterThanOrEqual(4);
        expect(District::count())->toBe(12);
        expect(MembershipTier::count())->toBe(3);
        expect(Customer::count())->toBeGreaterThanOrEqual(2);
        expect(Booking::count())->toBeGreaterThanOrEqual(1);
        expect(BookingItem::count())->toBeGreaterThanOrEqual(1);
    });

    test('specialist to services many-to-many relationship is established', function () {
        $karen = Specialist::where('slug', 'sp_karen')->first();
        expect($karen)->not->toBeNull();
        expect($karen->services()->count())->toBeGreaterThanOrEqual(1);
    });

    test('service pricing tiers link correctly to hair lengths', function () {
        $tier = ServicePricingTier::with(['service', 'hairLength'])->first();
        expect($tier)->not->toBeNull();
        expect($tier->service)->not->toBeNull();
        expect($tier->price)->toBeGreaterThan(0);
    });
});

describe('Spatie Translatable JSON', function () {
    test('models store and retrieve translations seamlessly', function () {
        $service = Service::first();
        expect($service)->not->toBeNull();

        $service->setTranslation('name', 'ar', 'تسريحة ملكية خاصة');
        $service->setTranslation('name', 'en', 'Royal Signature Hairstyle');
        $service->save();

        app()->setLocale('ar');
        expect($service->getTranslation('name', 'ar'))->toBe('تسريحة ملكية خاصة');

        app()->setLocale('en');
        expect($service->getTranslation('name', 'en'))->toBe('Royal Signature Hairstyle');

        // Reset locale
        app()->setLocale('ar');
    });
});

describe('Professional Caching & Auto-Invalidation', function () {
    test('services and salon identity are cached and served from cache', function () {
        $salonService = app(SalonInfoDataService::class);
        $identity = $salonService->getSalonIdentity();
        expect($identity['name_ar'])->toBe('هيرلاين');

        expect(Cache::has(HairlineCacheService::KEY_SALON_IDENTITY))->toBeTrue();

        $catalogService = app(CatalogDataService::class);
        $catalog = $catalogService->getAllCategories();
        expect($catalog)->toBeArray();
        expect(count($catalog))->toBe(7);

        expect(Cache::has(HairlineCacheService::KEY_ALL_CATEGORIES))->toBeTrue();
    });

    test('updating a service invalidates catalog cache via observer', function () {
        app(CatalogDataService::class)->getAllCategories();
        expect(Cache::has(HairlineCacheService::KEY_ALL_CATEGORIES))->toBeTrue();

        // Updating a service triggers ServiceObserver
        $service = Service::first();
        $service->update(['is_popular' => ! $service->is_popular]);

        expect(Cache::has(HairlineCacheService::KEY_ALL_CATEGORIES))->toBeFalse();
    });

    test('updating a specialist invalidates specialists cache via observer', function () {
        app(SpecialistsDataService::class)->getAllSpecialists();
        expect(Cache::has(HairlineCacheService::KEY_SPECIALISTS_ALL))->toBeTrue();

        // Updating specialist triggers SpecialistObserver
        $sp = Specialist::first();
        $sp->update(['is_active' => true]);

        expect(Cache::has(HairlineCacheService::KEY_SPECIALISTS_ALL))->toBeFalse();
    });
});

describe('Specialist Availability & Conflict Prevention', function () {
    test('detects appointment overlap using interval logic', function () {
        $specialist = Specialist::where('slug', 'sp_karen')->first() ?? Specialist::first();
        $customer = Customer::first();
        $service = Service::first();

        $date = Carbon::tomorrow()->toDateString();

        // Existing booking: 14:00 to 16:00
        $booking = Booking::create([
            'booking_number' => 'TEST-'.rand(1000, 9999),
            'customer_id' => $customer->id,
            'booking_type' => 'salon',
            'booking_date' => $date,
            'overall_start_time' => '14:00:00',
            'overall_end_time' => '16:00:00',
            'status' => 'confirmed',
            'subtotal_amount' => 500,
            'total_amount' => 500,
            'deposit_required' => 150,
            'deposit_paid' => 150,
        ]);

        BookingItem::create([
            'booking_id' => $booking->id,
            'service_id' => $service->id,
            'specialist_id' => $specialist->id,
            'unit_price' => 500,
            'duration_minutes' => 120,
            'scheduled_start_time' => '14:00:00',
            'scheduled_end_time' => '16:00:00',
            'status' => 'confirmed',
        ]);

        // Helper to check overlap: B_start < T_end AND B_end > T_start
        $isBusy = function ($reqStart, $reqEnd) use ($specialist, $date) {
            return BookingItem::where('specialist_id', $specialist->id)
                ->whereHas('booking', fn ($q) => $q->whereDate('booking_date', $date)->whereIn('status', ['confirmed', 'pending', 'in_progress']))
                ->where(function ($query) use ($reqStart, $reqEnd) {
                    $query->where('scheduled_start_time', '<', $reqEnd)
                        ->where('scheduled_end_time', '>', $reqStart);
                })
                ->exists();
        };

        // 1. Overlapping slot (15:00 to 17:00) -> MUST BE BUSY
        expect($isBusy('15:00:00', '17:00:00'))->toBeTrue();

        // 2. Enclosed slot (14:30 to 15:30) -> MUST BE BUSY
        expect($isBusy('14:30:00', '15:30:00'))->toBeTrue();

        // 3. Free slot before (12:00 to 14:00) -> AVAILABLE
        expect($isBusy('12:00:00', '14:00:00'))->toBeFalse();

        // 4. Free slot after (16:30 to 18:00) -> AVAILABLE
        expect($isBusy('16:30:00', '18:00:00'))->toBeFalse();
    });
});
