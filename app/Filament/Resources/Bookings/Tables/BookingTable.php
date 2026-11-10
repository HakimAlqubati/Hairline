<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bookings\Tables;

use App\Models\Booking;
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

                TextColumn::make('customer_type')
                    ->label('نوع العميل')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        Booking::CUSTOMER_TYPE_GUEST => 'زائر',
                        Booking::CUSTOMER_TYPE_REGISTERED => 'عميل مسجل',
                        default => $state ?? 'عميل مسجل',
                    })
                    ->color(fn ($state) => match ($state) {
                        Booking::CUSTOMER_TYPE_GUEST => 'warning',
                        Booking::CUSTOMER_TYPE_REGISTERED => 'success',
                        default => 'gray',
                    })
                    ->sortable()
                    ->hidden(),

                TextColumn::make('guests_count')
                    ->label('العدد')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

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
                    })
                    ->hidden(),

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

                SelectFilter::make('customer_type')
                    ->label('نوع العميل')
                    ->options([
                        Booking::CUSTOMER_TYPE_REGISTERED => 'عميل مسجل',
                    ])
                    ->hidden(),

                SelectFilter::make('booking_type')
                    ->label(__('admin.fields.booking_type'))
                    ->options([
                        'salon' => __('admin.options.booking_type_salon'),
                        'home' => __('admin.options.booking_type_home'),
                    ])
                    ->hidden(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
