<?php

declare(strict_types=1);

namespace App\Filament\Resources\CatalogPages;

use App\Filament\Resources\CatalogPages\Pages\CreateCatalogPage;
use App\Filament\Resources\CatalogPages\Pages\EditCatalogPage;
use App\Filament\Resources\CatalogPages\Pages\ListCatalogPages;
use App\Filament\Resources\CatalogPages\Schemas\CatalogPageForm;
use App\Filament\Resources\CatalogPages\Tables\CatalogPageTable;
use App\Models\CatalogPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CatalogPageResource extends Resource
{
    protected static ?string $model = CatalogPage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.services_catalog');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.catalog_page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.catalog_pages');
    }

    public static function form(Schema $schema): Schema
    {
        return CatalogPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CatalogPageTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCatalogPages::route('/'),
            'create' => CreateCatalogPage::route('/create'),
            'edit' => EditCatalogPage::route('/{record}/edit'),
        ];
    }
}
