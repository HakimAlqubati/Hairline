<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Messaging\OtpChannelInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\OtpRepositoryInterface;
use App\Contracts\Sms\SmsGatewayInterface;
use App\Contracts\Whatsapp\WhatsappServiceInterface;
use App\Enums\BrandColor;
use App\Models\CatalogPage;
use App\Models\HeroSlide;
use App\Models\SalonGallery;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServicePricingTier;
use App\Models\Specialist;
use App\Models\SpecialistShift;
use App\Observers\SalonSettingObserver;
use App\Observers\ServiceObserver;
use App\Observers\SpecialistObserver;
use App\Repositories\Customer\EloquentCustomerRepository;
use App\Repositories\Otp\CacheOtpRepository;
use App\Services\LogoProcessor;
use App\Services\Sms\SimulatedSmsGateway;
use App\Services\Whatsapp\SimulatedWhatsappService;
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
        $this->app->bind(SmsGatewayInterface::class, SimulatedSmsGateway::class);
        $this->app->bind(WhatsappServiceInterface::class, SimulatedWhatsappService::class);
        $this->app->bind(OtpChannelInterface::class, function ($app) {
            return match (config('sms.channel', 'sms')) {
                'whatsapp' => $app->make(WhatsappServiceInterface::class),
                default => $app->make(SmsGatewayInterface::class),
            };
        });
        $this->app->bind(OtpRepositoryInterface::class, CacheOtpRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
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
                ])
                ->userPreferredLocale(fn (): string => request()->cookie('filament_language_switch_locale') ?? config('app.locale', 'ar'));
        });

        // Register Cache Invalidation Observers
        Service::observe(ServiceObserver::class);
        ServiceCategory::observe(ServiceObserver::class);
        ServicePricingTier::observe(ServiceObserver::class);
        CatalogPage::observe(ServiceObserver::class);

        Specialist::observe(SpecialistObserver::class);
        SpecialistShift::observe(SpecialistObserver::class);

        HeroSlide::observe(SalonSettingObserver::class);
        SalonGallery::observe(SalonSettingObserver::class);
    }
}
