<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteVisits;

use App\Filament\Resources\SiteVisits\Pages\ListSiteVisits;
use App\Filament\Resources\SiteVisits\Tables\SiteVisitTable;
use App\Models\SiteVisit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SiteVisitResource extends Resource
{
    protected static ?string $model = SiteVisit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.website_content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.site_visit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.site_visits');
    }

    public static function table(Table $table): Table
    {
        return SiteVisitTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteVisits::route('/'),
        ];
    }
}
