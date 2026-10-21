<?php

declare(strict_types=1);

namespace App\Filament\Resources\Specialists;

use App\Filament\Resources\Specialists\Pages\CreateSpecialist;
use App\Filament\Resources\Specialists\Pages\EditSpecialist;
use App\Filament\Resources\Specialists\Pages\ListSpecialists;
use App\Filament\Resources\Specialists\Schemas\SpecialistForm;
use App\Filament\Resources\Specialists\Tables\SpecialistTable;
use App\Models\Specialist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SpecialistResource extends Resource
{
    protected static ?string $model = Specialist::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.specialists_team');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.specialist');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.specialists');
    }

    public static function form(Schema $schema): Schema
    {
        return SpecialistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecialistTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecialists::route('/'),
            'create' => CreateSpecialist::route('/create'),
            'edit' => EditSpecialist::route('/{record}/edit'),
        ];
    }
}
