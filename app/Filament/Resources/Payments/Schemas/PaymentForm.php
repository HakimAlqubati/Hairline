<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('booking_id')
                                ->label(__('admin.fields.booking'))
                                ->relationship('booking', 'booking_number')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('payment_method_id')
                                ->label(__('admin.fields.payment_method'))
                                ->relationship('paymentMethod', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('amount')
                                ->label(__('admin.fields.amount'))
                                ->numeric()
                                ->prefix('SAR')
                                ->required(),

                            Select::make('payment_type')
                                ->label(__('admin.fields.payment_type'))
                                ->options([
                                    'deposit' => __('admin.options.payment_type_deposit'),
                                    'final' => __('admin.options.payment_type_final'),
                                    'refund' => __('admin.options.payment_type_refund'),
                                ])
                                ->default('deposit')
                                ->required(),

                            Select::make('status')
                                ->label(__('admin.fields.status'))
                                ->options([
                                    'success' => __('admin.statuses.success'),
                                    'pending' => __('admin.statuses.pending'),
                                    'failed' => __('admin.statuses.failed'),
                                ])
                                ->default('success')
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('transaction_reference')
                                ->label(__('admin.fields.transaction_reference'))
                                ->nullable()
                                ->maxLength(255),

                            DateTimePicker::make('paid_at')
                                ->label(__('admin.fields.paid_at'))
                                ->default(now())
                                ->nullable(),
                        ]),
                    ]),
            ]);
    }
}
