<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonWorkingHours;

use App\Filament\Resources\SalonWorkingHours\Pages\CreateSalonWorkingHour;
use App\Filament\Resources\SalonWorkingHours\Pages\EditSalonWorkingHour;
use App\Filament\Resources\SalonWorkingHours\Pages\ListSalonWorkingHours;
use App\Filament\Resources\SalonWorkingHours\Schemas\SalonWorkingHourForm;
use App\Filament\Resources\SalonWorkingHours\Tables\SalonWorkingHourTable;
use App\Models\SalonWorkingHour;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SalonWorkingHourResource extends Resource
{
    protected static ?string $model = SalonWorkingHour::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.website_content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.salon_working_hour');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.salon_working_hours');
    }

    public static function form(Schema $schema): Schema
    {
        return SalonWorkingHourForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalonWorkingHourTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalonWorkingHours::route('/'),
            'create' => CreateSalonWorkingHour::route('/create'),
            'edit' => EditSalonWorkingHour::route('/{record}/edit'),
        ];
    }
}
