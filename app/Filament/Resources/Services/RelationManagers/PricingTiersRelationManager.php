<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PricingTiersRelationManager extends RelationManager
{
    protected static string $relationship = 'pricingTiers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.pricing_tiers');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('hair_length_id')
                        ->label(__('admin.fields.hair_length'))
                        ->relationship('hairLength', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('custom_label')
                        ->label(__('admin.fields.custom_label'))
                        ->nullable(),
                ]),

                Grid::make(3)->schema([
                    TextInput::make('price')
                        ->label(__('admin.fields.price'))
                        ->numeric()
                        ->prefix('SAR')
                        ->required(),

                    TextInput::make('extra_duration_minutes')
                        ->label(__('admin.fields.extra_duration_minutes'))
                        ->numeric()
                        ->default(0)
                        ->suffix(__('admin.units.minutes')),

                    TextInput::make('sort_order')
                        ->label(__('admin.fields.sort_order'))
                        ->numeric()
                        ->default(0),
                ]),

                Toggle::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('custom_label')
            ->columns([
                TextColumn::make('hairLength.name')
                    ->label(__('admin.fields.hair_length'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('custom_label')
                    ->label(__('admin.fields.custom_label')),

                TextColumn::make('price')
                    ->label(__('admin.fields.price'))
                    ->money('SAR')
                    ->sortable(),

                TextColumn::make('extra_duration_minutes')
                    ->label(__('admin.fields.extra_duration_minutes'))
                    ->suffix(' '.__('admin.units.minutes')),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
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
