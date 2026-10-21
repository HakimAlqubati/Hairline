<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CustomerTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('admin.fields.avatar'))
                    ->circular(),

                TextColumn::make('customer_code')
                    ->label(__('admin.fields.customer_code'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('admin.fields.phone'))
                    ->searchable(),

                TextColumn::make('membershipTier.name')
                    ->label(__('admin.fields.membership_tier'))
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('total_points')
                    ->label(__('admin.fields.total_points'))
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('bookings_count')
                    ->label(__('admin.resources.bookings'))
                    ->counts('bookings')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('membership_tier_id')
                    ->label(__('admin.fields.membership_tier'))
                    ->relationship('membershipTier', 'name'),

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
