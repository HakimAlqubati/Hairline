<?php

declare(strict_types=1);

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('admin.fields.name'))
                                ->placeholder('مدى / Mada')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('code')
                                ->label(__('admin.fields.code'))
                                ->placeholder('mada, apple_pay, cash, tabby')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                        ]),

                        Textarea::make('instructions')
                            ->label(__('admin.fields.instructions'))
                            ->rows(3)
                            ->nullable(),

                        Grid::make(2)->schema([
                            Toggle::make('is_online')
                                ->label(__('admin.fields.is_online'))
                                ->default(false),

                            Toggle::make('is_active')
                                ->label(__('admin.fields.is_active'))
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
