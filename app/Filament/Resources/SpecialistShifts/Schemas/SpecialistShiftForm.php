<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistShifts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SpecialistShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.work_schedule'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('specialist_id')
                                ->label(__('admin.fields.specialist'))
                                ->relationship('specialist', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('day_of_week')
                                ->label(__('admin.fields.day_of_week'))
                                ->options(__('admin.options.days'))
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TimePicker::make('start_time')
                                ->label(__('admin.fields.start_time'))
                                ->required(),

                            TimePicker::make('end_time')
                                ->label(__('admin.fields.end_time'))
                                ->required(),
                        ]),

                        Toggle::make('is_day_off')
                            ->label(__('admin.fields.is_day_off'))
                            ->default(false),
                    ]),
            ]);
    }
}
