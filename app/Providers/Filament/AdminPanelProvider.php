<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->favicon(asset('images/favicon.png'))
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo-dark.png'))
            ->brandLogoHeight('4.0rem')
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::hex(config('brand.primary')),
                'gray' => Color::Zinc,
            ])
            ->font('Tajawal')
            ->brandName(fn (): string => __('admin.brand.name'))
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.website_content'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.services_catalog'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.specialists_team'))
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.bookings'))
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.customers'))
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.loyalty_program'))
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.financials_logistics'))
                    ->collapsed(true),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.system_settings'))
                    ->collapsed(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}
