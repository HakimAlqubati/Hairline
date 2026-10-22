<?php

declare(strict_types=1);

namespace App\Filament\Resources\PaymentMethods\Schemas;

use App\Filament\Support\TranslatableField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        TranslatableField::text('name', __('admin.fields.name'), required: true),

                        TextInput::make('code')
                            ->label(__('admin.fields.code'))
                            ->placeholder('mada, apple_pay, cash, tabby')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TranslatableField::textarea('description', __('admin.fields.description')),

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
