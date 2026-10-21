<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CustomerAddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.customer_addresses');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('district_id')
                    ->label(__('admin.fields.district'))
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Grid::make(3)->schema([
                    TextInput::make('street_name')
                        ->label(__('admin.fields.street_name'))
                        ->nullable(),

                    TextInput::make('building_number')
                        ->label(__('admin.fields.building_number'))
                        ->nullable(),

                    TextInput::make('floor_number')
                        ->label(__('admin.fields.floor_number'))
                        ->nullable(),
                ]),

                Grid::make(2)->schema([
                    TextInput::make('latitude')
                        ->label(__('admin.fields.latitude'))
                        ->numeric()
                        ->nullable(),

                    TextInput::make('longitude')
                        ->label(__('admin.fields.longitude'))
                        ->numeric()
                        ->nullable(),
                ]),

                Textarea::make('notes')
                    ->label(__('admin.fields.notes'))
                    ->rows(2)
                    ->nullable(),

                Toggle::make('is_default')
                    ->label(__('admin.fields.is_default'))
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street_name')
            ->columns([
                TextColumn::make('district.name')
                    ->label(__('admin.fields.district'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('street_name')
                    ->label(__('admin.fields.street_name')),

                TextColumn::make('building_number')
                    ->label(__('admin.fields.building_number')),

                IconColumn::make('is_default')
                    ->label(__('admin.fields.is_default'))
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
