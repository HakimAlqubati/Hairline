<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteVisits\Pages;

use App\Filament\Resources\SiteVisits\SiteVisitResource;
use App\Filament\Resources\SiteVisits\Widgets\SiteVisitStatsOverview;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListSiteVisits extends ListRecords
{
    protected static string $resource = SiteVisitResource::class;

    public function getHeading(): string|Htmlable
    {
        return __('admin.site_visits.heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('admin.site_visits.subheading');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SiteVisitStatsOverview::class,
        ];
    }
}
