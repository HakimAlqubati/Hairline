<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyPointTransactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LoyaltyPointTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('customer_id')
                                ->label(__('admin.fields.customer'))
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('booking_id')
                                ->label(__('admin.fields.booking'))
                                ->relationship('booking', 'booking_number')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('points_change')
                                ->label(__('admin.fields.points_change'))
                                ->numeric()
                                ->helperText('مثال: 50 لإضافة نقاط، أو -20 لخصم نقاط')
                                ->required(),

                            TextInput::make('balance_after')
                                ->label(__('admin.fields.balance_after'))
                                ->numeric()
                                ->required(),
                        ]),

                        Textarea::make('description')
                            ->label(__('admin.fields.description'))
                            ->rows(3)
                            ->required(),
                    ]),
            ]);
    }
}
