<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonWorkingHours\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalonWorkingHourForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('day_of_week')
                                ->label(__('admin.fields.day_of_week'))
                                ->options(__('admin.options.days'))
                                ->required(),

                            TextInput::make('day_label')
                                ->label(__('admin.fields.day_label'))
                                ->placeholder('مثال: الأحد إلى الخميس')
                                ->nullable()
                                ->maxLength(255),
                        ]),

                        Grid::make(2)->schema([
                            TimePicker::make('open_time')
                                ->label(__('admin.fields.open_time'))
                                ->required(),

                            TimePicker::make('close_time')
                                ->label(__('admin.fields.close_time'))
                                ->required(),
                        ]),

                        Toggle::make('is_closed')
                            ->label(__('admin.fields.is_closed'))
                            ->default(false),

                        Textarea::make('notes')
                            ->label(__('admin.fields.notes'))
                            ->rows(2)
                            ->nullable(),
                    ]),
            ]);
    }
}
