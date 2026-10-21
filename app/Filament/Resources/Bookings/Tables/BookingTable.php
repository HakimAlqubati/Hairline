<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_number')
                    ->label(__('admin.fields.booking_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('customer.name')
                    ->label(__('admin.fields.customer'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('booking_type')
                    ->label(__('admin.fields.booking_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'salon' => __('admin.options.booking_type_salon'),
                        'home' => __('admin.options.booking_type_home'),
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'salon' => 'info',
                        'home' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('booking_date')
                    ->label(__('admin.fields.booking_date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('overall_start_time')
                    ->label(__('admin.fields.overall_start_time'))
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('admin.statuses.'.$state) ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled', 'no_show' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label(__('admin.fields.payment_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('admin.statuses.'.$state) ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'fully_paid' => 'success',
                        'partially_paid' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('total_amount')
                    ->label(__('admin.fields.total_amount'))
                    ->money('SAR')
                    ->sortable(),
            ])
            ->defaultSort('booking_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options([
                        'pending' => __('admin.statuses.pending'),
                        'confirmed' => __('admin.statuses.confirmed'),
                        'in_progress' => __('admin.statuses.in_progress'),
                        'completed' => __('admin.statuses.completed'),
                        'cancelled' => __('admin.statuses.cancelled'),
                    ]),

                SelectFilter::make('booking_type')
                    ->label(__('admin.fields.booking_type'))
                    ->options([
                        'salon' => __('admin.options.booking_type_salon'),
                        'home' => __('admin.options.booking_type_home'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
