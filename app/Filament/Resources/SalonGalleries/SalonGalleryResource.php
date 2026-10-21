<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonGalleries;

use App\Filament\Resources\SalonGalleries\Pages\CreateSalonGallery;
use App\Filament\Resources\SalonGalleries\Pages\EditSalonGallery;
use App\Filament\Resources\SalonGalleries\Pages\ListSalonGalleries;
use App\Filament\Resources\SalonGalleries\Schemas\SalonGalleryForm;
use App\Filament\Resources\SalonGalleries\Tables\SalonGalleryTable;
use App\Models\SalonGallery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SalonGalleryResource extends Resource
{
    protected static ?string $model = SalonGallery::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-camera';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.website_content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.salon_gallery');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.salon_galleries');
    }

    public static function form(Schema $schema): Schema
    {
        return SalonGalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalonGalleryTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalonGalleries::route('/'),
            'create' => CreateSalonGallery::route('/create'),
            'edit' => EditSalonGallery::route('/{record}/edit'),
        ];
    }
}
