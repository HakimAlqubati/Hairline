<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistUnavailabilities\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SpecialistUnavailabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        Select::make('specialist_id')
                            ->label(__('admin.fields.specialist'))
                            ->relationship('specialist', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Grid::make(2)->schema([
                            DateTimePicker::make('start_datetime')
                                ->label(__('admin.fields.start_datetime'))
                                ->required(),

                            DateTimePicker::make('end_datetime')
                                ->label(__('admin.fields.end_datetime'))
                                ->required(),
                        ]),

                        Textarea::make('reason')
                            ->label(__('admin.fields.reason'))
                            ->rows(3)
                            ->nullable(),
                    ]),
            ]);
    }
}
