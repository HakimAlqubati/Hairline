<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.booking_info'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('booking_number')
                                ->label(__('admin.fields.booking_number'))
                                ->default(fn () => 'HL-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(2))))
                                ->required()
                                ->unique(ignoreRecord: true),

                            Select::make('customer_id')
                                ->label(__('admin.fields.customer'))
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('booking_type')
                                ->label(__('admin.fields.booking_type'))
                                ->options([
                                    'salon' => __('admin.options.booking_type_salon'),
                                    'home' => __('admin.options.booking_type_home'),
                                ])
                                ->default('salon')
                                ->required(),
                        ]),

                        Grid::make(3)->schema([
                            DatePicker::make('booking_date')
                                ->label(__('admin.fields.booking_date'))
                                ->required(),

                            TimePicker::make('overall_start_time')
                                ->label(__('admin.fields.overall_start_time'))
                                ->required(),

                            TimePicker::make('overall_end_time')
                                ->label(__('admin.fields.overall_end_time'))
                                ->required(),
                        ]),

                        Select::make('customer_address_id')
                            ->label(__('admin.fields.customer_address'))
                            ->relationship('customerAddress', 'street_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),

                Section::make(__('admin.sections.financial_summary'))
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('subtotal_amount')
                                ->label(__('admin.fields.subtotal_amount'))
                                ->numeric()
                                ->prefix('SAR')
                                ->default(0.00),

                            TextInput::make('discount_amount')
                                ->label(__('admin.fields.discount_amount'))
                                ->numeric()
                                ->prefix('SAR')
                                ->default(0.00),

                            TextInput::make('vat_amount')
                                ->label(__('admin.fields.vat_amount'))
                                ->numeric()
                                ->prefix('SAR')
                                ->default(0.00),

                            TextInput::make('total_amount')
                                ->label(__('admin.fields.total_amount'))
                                ->numeric()
                                ->prefix('SAR')
                                ->default(0.00),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('deposit_required')
                                ->label(__('admin.fields.deposit_required'))
                                ->numeric()
                                ->prefix('SAR')
                                ->default(0.00),

                            TextInput::make('deposit_paid')
                                ->label(__('admin.fields.deposit_paid'))
                                ->numeric()
                                ->prefix('SAR')
                                ->default(0.00),

                            Select::make('payment_status')
                                ->label(__('admin.fields.payment_status'))
                                ->options([
                                    'unpaid' => __('admin.statuses.unpaid'),
                                    'partially_paid' => __('admin.statuses.partially_paid'),
                                    'fully_paid' => __('admin.statuses.fully_paid'),
                                    'refunded' => __('admin.statuses.refunded'),
                                ])
                                ->default('unpaid')
                                ->required(),
                        ]),
                    ]),

                Section::make(__('admin.sections.status_and_notes'))
                    ->schema([
                        Select::make('status')
                            ->label(__('admin.fields.status'))
                            ->options([
                                'pending' => __('admin.statuses.pending'),
                                'confirmed' => __('admin.statuses.confirmed'),
                                'in_progress' => __('admin.statuses.in_progress'),
                                'completed' => __('admin.statuses.completed'),
                                'cancelled' => __('admin.statuses.cancelled'),
                                'no_show' => __('admin.statuses.no_show'),
                            ])
                            ->default('confirmed')
                            ->required(),

                        Textarea::make('admin_notes')
                            ->label(__('admin.fields.admin_notes'))
                            ->rows(3)
                            ->nullable(),
                    ]),
            ]);
    }
}
