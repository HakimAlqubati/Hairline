<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyPointTransactions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LoyaltyPointTransactionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label(__('admin.fields.customer'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('booking.booking_number')
                    ->label(__('admin.fields.booking_number'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('points_change')
                    ->label(__('admin.fields.points_change'))
                    ->badge()
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '').$state.' '.__('admin.units.points'))
                    ->sortable(),

                TextColumn::make('balance_after')
                    ->label(__('admin.fields.balance_after'))
                    ->numeric()
                    ->suffix(' '.__('admin.units.points'))
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('admin.fields.description'))
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('customer_id')
                    ->label(__('admin.filters.customer'))
                    ->relationship('customer', 'name'),
            ]);
    }
}
