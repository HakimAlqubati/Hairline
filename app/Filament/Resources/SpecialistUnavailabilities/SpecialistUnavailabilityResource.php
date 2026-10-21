<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistUnavailabilities;

use App\Filament\Resources\SpecialistUnavailabilities\Pages\CreateSpecialistUnavailability;
use App\Filament\Resources\SpecialistUnavailabilities\Pages\EditSpecialistUnavailability;
use App\Filament\Resources\SpecialistUnavailabilities\Pages\ListSpecialistUnavailabilities;
use App\Filament\Resources\SpecialistUnavailabilities\Schemas\SpecialistUnavailabilityForm;
use App\Filament\Resources\SpecialistUnavailabilities\Tables\SpecialistUnavailabilityTable;
use App\Models\SpecialistUnavailability;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SpecialistUnavailabilityResource extends Resource
{
    protected static ?string $model = SpecialistUnavailability::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.specialists_team');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.specialist_unavailability');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.specialist_unavailabilities');
    }

    public static function form(Schema $schema): Schema
    {
        return SpecialistUnavailabilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecialistUnavailabilityTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecialistUnavailabilities::route('/'),
            'create' => CreateSpecialistUnavailability::route('/create'),
            'edit' => EditSpecialistUnavailability::route('/{record}/edit'),
        ];
    }
}
