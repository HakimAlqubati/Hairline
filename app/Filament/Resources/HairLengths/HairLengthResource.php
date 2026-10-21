<?php

declare(strict_types=1);

namespace App\Filament\Resources\HairLengths;

use App\Filament\Resources\HairLengths\Pages\CreateHairLength;
use App\Filament\Resources\HairLengths\Pages\EditHairLength;
use App\Filament\Resources\HairLengths\Pages\ListHairLengths;
use App\Filament\Resources\HairLengths\Schemas\HairLengthForm;
use App\Filament\Resources\HairLengths\Tables\HairLengthTable;
use App\Models\HairLength;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class HairLengthResource extends Resource
{
    protected static ?string $model = HairLength::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.services_catalog');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.hair_length');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.hair_lengths');
    }

    public static function form(Schema $schema): Schema
    {
        return HairLengthForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HairLengthTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHairLengths::route('/'),
            'create' => CreateHairLength::route('/create'),
            'edit' => EditHairLength::route('/{record}/edit'),
        ];
    }
}
