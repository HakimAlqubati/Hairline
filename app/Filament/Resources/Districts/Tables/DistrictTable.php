<?php

declare(strict_types=1);

namespace App\Filament\Resources\Districts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DistrictTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('zone')
                    ->label(__('admin.fields.zone'))
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('delivery_fee')
                    ->label(__('admin.fields.delivery_fee'))
                    ->money('SAR')
                    ->sortable(),

                TextColumn::make('estimated_transit_minutes')
                    ->label(__('admin.fields.estimated_transit_minutes'))
                    ->suffix(' '.__('admin.units.minutes'))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('admin.filters.active_status')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
