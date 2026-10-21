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
use Filament\Widgets\AccountWidget;
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
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Rose,
                'gray' => Color::Zinc,
            ])
            ->font('Tajawal')
            ->brandName(fn (): string => __('admin.brand.name'))
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.bookings'))
                    ->icon('heroicon-o-calendar-days')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.services_catalog'))
                    ->icon('heroicon-o-sparkles')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.specialists_team'))
                    ->icon('heroicon-o-user-group')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.customers_loyalty'))
                    ->icon('heroicon-o-heart')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.website_content'))
                    ->icon('heroicon-o-globe-alt')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.financials_logistics'))
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(true),

                NavigationGroup::make()
                    ->label(fn (): string => __('admin.nav.system_settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
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
            ]);
    }
}
