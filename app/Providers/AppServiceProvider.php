<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\BrandColor;
use App\Models\CatalogPage;
use App\Models\FounderStory;
use App\Models\HeroSlide;
use App\Models\SalonGallery;
use App\Models\SalonSetting;
use App\Models\SalonWorkingHour;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServicePricingTier;
use App\Models\Specialist;
use App\Models\SpecialistShift;
use App\Observers\SalonSettingObserver;
use App\Observers\ServiceObserver;
use App\Observers\SpecialistObserver;
use App\Services\LogoProcessor;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LogoProcessor::ensureLogosExist();

        View::share('brandPrimaryColor', BrandColor::Primary->value);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch): void {
            $switch
                ->locales(['ar', 'en'])
                ->labels([
                    'ar' => 'العربية',
                    'en' => 'English',
                ]);
        });

        // Register Cache Invalidation Observers
        Service::observe(ServiceObserver::class);
        ServiceCategory::observe(ServiceObserver::class);
        ServicePricingTier::observe(ServiceObserver::class);
        CatalogPage::observe(ServiceObserver::class);

        Specialist::observe(SpecialistObserver::class);
        SpecialistShift::observe(SpecialistObserver::class);

        SalonSetting::observe(SalonSettingObserver::class);
        HeroSlide::observe(SalonSettingObserver::class);
        SalonGallery::observe(SalonSettingObserver::class);
        FounderStory::observe(SalonSettingObserver::class);
        SalonWorkingHour::observe(SalonSettingObserver::class);
    }
}
