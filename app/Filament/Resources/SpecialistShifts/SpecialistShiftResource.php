<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistShifts;

use App\Filament\Resources\SpecialistShifts\Pages\CreateSpecialistShift;
use App\Filament\Resources\SpecialistShifts\Pages\EditSpecialistShift;
use App\Filament\Resources\SpecialistShifts\Pages\ListSpecialistShifts;
use App\Filament\Resources\SpecialistShifts\Schemas\SpecialistShiftForm;
use App\Filament\Resources\SpecialistShifts\Tables\SpecialistShiftTable;
use App\Models\SpecialistShift;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SpecialistShiftResource extends Resource
{
    protected static ?string $model = SpecialistShift::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.specialists_team');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.specialist_shift');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.specialist_shifts');
    }

    public static function form(Schema $schema): Schema
    {
        return SpecialistShiftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecialistShiftTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecialistShifts::route('/'),
            'create' => CreateSpecialistShift::route('/create'),
            'edit' => EditSpecialistShift::route('/{record}/edit'),
        ];
    }
}
