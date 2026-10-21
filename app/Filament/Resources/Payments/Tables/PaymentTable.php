<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.booking_number')
                    ->label(__('admin.fields.booking_number'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('paymentMethod.name')
                    ->label(__('admin.fields.payment_method'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label(__('admin.fields.amount'))
                    ->money('SAR')
                    ->sortable(),

                TextColumn::make('payment_type')
                    ->label(__('admin.fields.payment_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'deposit' => __('admin.options.payment_type_deposit'),
                        'final' => __('admin.options.payment_type_final'),
                        'refund' => __('admin.options.payment_type_refund'),
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'deposit' => 'warning',
                        'final' => 'success',
                        'refund' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'success' => __('admin.statuses.success'),
                        'pending' => __('admin.statuses.pending'),
                        'failed' => __('admin.statuses.failed'),
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('transaction_reference')
                    ->label(__('admin.fields.transaction_reference'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('paid_at')
                    ->label(__('admin.fields.paid_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('paid_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options([
                        'success' => __('admin.statuses.success'),
                        'pending' => __('admin.statuses.pending'),
                        'failed' => __('admin.statuses.failed'),
                    ]),

                SelectFilter::make('payment_method_id')
                    ->label(__('admin.fields.payment_method'))
                    ->relationship('paymentMethod', 'name'),
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
