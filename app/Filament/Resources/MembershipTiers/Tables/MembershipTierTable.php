<?php

declare(strict_types=1);

namespace App\Filament\Resources\MembershipTiers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MembershipTierTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('badge_color')
                    ->label(__('admin.fields.badge_color')),

                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('min_spend')
                    ->label(__('admin.fields.min_spend'))
                    ->money('SAR')
                    ->sortable(),

                TextColumn::make('discount_percentage')
                    ->label(__('admin.fields.discount_percentage'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('points_multiplier')
                    ->label(__('admin.fields.points_multiplier'))
                    ->suffix('x')
                    ->sortable(),

                TextColumn::make('customers_count')
                    ->label(__('admin.resources.customers'))
                    ->counts('customers')
                    ->badge()
                    ->color('info')
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
