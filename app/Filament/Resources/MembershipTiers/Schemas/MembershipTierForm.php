<?php

declare(strict_types=1);

namespace App\Filament\Resources\MembershipTiers\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MembershipTierForm
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
                                ->placeholder('الذهبية / Gold')
                                ->required()
                                ->maxLength(255),

                            ColorPicker::make('badge_color')
                                ->label(__('admin.fields.badge_color'))
                                ->default('#D97706'),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('min_spend')
                                ->label(__('admin.fields.min_spend'))
                                ->numeric()
                                ->prefix('SAR')
                                ->required(),

                            TextInput::make('discount_percentage')
                                ->label(__('admin.fields.discount_percentage'))
                                ->numeric()
                                ->suffix('%')
                                ->default(0),

                            TextInput::make('points_multiplier')
                                ->label(__('admin.fields.points_multiplier'))
                                ->numeric()
                                ->default(1.0),
                        ]),

                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
