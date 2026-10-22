<?php

declare(strict_types=1);

namespace App\Filament\Resources\Districts\Schemas;

use App\Filament\Support\TranslatableField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        TranslatableField::text('name', __('admin.fields.name'), required: true),

                        TextInput::make('zone')
                            ->label(__('admin.fields.zone'))
                            ->placeholder('شمال الرياض / North Riyadh')
                            ->nullable()
                            ->maxLength(255),

                        Grid::make(2)->schema([
                            TextInput::make('delivery_fee')
                                ->label(__('admin.fields.delivery_fee'))
                                ->numeric()
                                ->prefix('SAR')
                                ->default(0.00)
                                ->required(),

                            TextInput::make('estimated_transit_minutes')
                                ->label(__('admin.fields.estimated_transit_minutes'))
                                ->numeric()
                                ->suffix(__('admin.units.minutes'))
                                ->default(30)
                                ->required(),
                        ]),

                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
