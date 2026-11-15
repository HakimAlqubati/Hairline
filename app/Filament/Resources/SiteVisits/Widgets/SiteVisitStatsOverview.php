<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteVisits\Widgets;

use App\Models\SiteVisit;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SiteVisitStatsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $todayStartUtc = Carbon::now('Asia/Riyadh')->startOfDay()->utc()->toDateTimeString();
        $sevenDaysStartUtc = Carbon::now('Asia/Riyadh')->subDays(6)->startOfDay()->utc()->toDateTimeString();

        $summary = SiteVisit::query()
            ->selectRaw('COUNT(*) as total_visits')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_visitors')
            ->selectRaw('SUM(CASE WHEN visited_at >= ? THEN 1 ELSE 0 END) as today_visits', [$todayStartUtc])
            ->selectRaw('COUNT(DISTINCT CASE WHEN visited_at >= ? THEN session_id END) as today_unique_visitors', [$todayStartUtc])
            ->selectRaw('SUM(CASE WHEN visited_at >= ? THEN 1 ELSE 0 END) as last_7_days_visits', [$sevenDaysStartUtc])
            ->selectRaw('SUM(CASE WHEN device_type = ? THEN 1 ELSE 0 END) as mobile_visits', [SiteVisit::DEVICE_MOBILE])
            ->selectRaw('SUM(CASE WHEN device_type = ? THEN 1 ELSE 0 END) as desktop_visits', [SiteVisit::DEVICE_DESKTOP])
            ->selectRaw('SUM(CASE WHEN device_type = ? THEN 1 ELSE 0 END) as tablet_visits', [SiteVisit::DEVICE_TABLET])
            ->first();

        $totalVisits = (int) ($summary?->total_visits ?? 0);
        $uniqueVisitors = (int) ($summary?->unique_visitors ?? 0);
        $todayVisits = (int) ($summary?->today_visits ?? 0);
        $todayUnique = (int) ($summary?->today_unique_visitors ?? 0);
        $weekVisits = (int) ($summary?->last_7_days_visits ?? 0);
        $mobileVisits = (int) ($summary?->mobile_visits ?? 0);
        $desktopVisits = (int) ($summary?->desktop_visits ?? 0);
        $tabletVisits = (int) ($summary?->tablet_visits ?? 0);
        $desktopAndTabletVisits = $desktopVisits + $tabletVisits;

        $mobilePercent = $totalVisits > 0 ? (int) round(($mobileVisits / $totalVisits) * 100) : 0;
        $desktopTabletPercent = $totalVisits > 0 ? (int) round(($desktopAndTabletVisits / $totalVisits) * 100) : 0;

        return [
            Stat::make(
                __('admin.site_visits.stats.today_visits'),
                Number::format($todayVisits)
            )
                ->description(__('admin.site_visits.stats.today_unique_desc', ['count' => Number::format($todayUnique)]))
                ->descriptionIcon('heroicon-o-clock')
                ->color('primary'),

            Stat::make(
                __('admin.site_visits.stats.total_visits'),
                Number::format($totalVisits)
            )
                ->description(__('admin.site_visits.stats.total_visits_desc', [
                    'week' => Number::format($weekVisits),
                    'unique' => Number::format($uniqueVisitors),
                ]))
                ->descriptionIcon('heroicon-o-eye')
                ->color('info'),

            Stat::make(
                __('admin.site_visits.stats.mobile_visits'),
                Number::format($mobileVisits)
            )
                ->description(__('admin.site_visits.stats.of_total_desc', ['percent' => $mobilePercent]))
                ->descriptionIcon('heroicon-o-device-phone-mobile')
                ->color('success'),

            Stat::make(
                __('admin.site_visits.stats.desktop_tablet_visits'),
                Number::format($desktopAndTabletVisits)." ({$desktopTabletPercent}%)"
            )
                ->description(__('admin.site_visits.stats.desktop_tablet_desc', [
                    'desktop' => Number::format($desktopVisits),
                    'tablet' => Number::format($tabletVisits),
                ]))
                ->descriptionIcon('heroicon-o-computer-desktop')
                ->color('warning'),
        ];
    }
}
