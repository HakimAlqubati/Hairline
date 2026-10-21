<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceGroups;

use App\Filament\Resources\ServiceGroups\Pages\CreateServiceGroup;
use App\Filament\Resources\ServiceGroups\Pages\EditServiceGroup;
use App\Filament\Resources\ServiceGroups\Pages\ListServiceGroups;
use App\Filament\Resources\ServiceGroups\Schemas\ServiceGroupForm;
use App\Filament\Resources\ServiceGroups\Tables\ServiceGroupTable;
use App\Models\ServiceGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ServiceGroupResource extends Resource
{
    protected static ?string $model = ServiceGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.services_catalog');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.service_group');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.service_groups');
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceGroupTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceGroups::route('/'),
            'create' => CreateServiceGroup::route('/create'),
            'edit' => EditServiceGroup::route('/{record}/edit'),
        ];
    }
}
