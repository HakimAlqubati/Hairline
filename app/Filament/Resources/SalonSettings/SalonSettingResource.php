<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonSettings;

use App\Filament\Resources\SalonSettings\Pages\CreateSalonSetting;
use App\Filament\Resources\SalonSettings\Pages\EditSalonSetting;
use App\Filament\Resources\SalonSettings\Pages\ListSalonSettings;
use App\Filament\Resources\SalonSettings\Schemas\SalonSettingForm;
use App\Filament\Resources\SalonSettings\Tables\SalonSettingTable;
use App\Models\SalonSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SalonSettingResource extends Resource
{
    protected static ?string $model = SalonSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.system_settings');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.salon_setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.salon_settings');
    }

    public static function form(Schema $schema): Schema
    {
        return SalonSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalonSettingTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalonSettings::route('/'),
            'create' => CreateSalonSetting::route('/create'),
            'edit' => EditSalonSetting::route('/{record}/edit'),
        ];
    }
}
