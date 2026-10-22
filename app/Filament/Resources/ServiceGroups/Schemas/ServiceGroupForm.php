<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceGroups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('category_id')
                                ->label(__('admin.fields.category'))
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            TextInput::make('name')
                                ->label(__('admin.fields.name'))
                                ->required()
                                ->maxLength(255),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('type')
                                ->label(__('admin.fields.type'))
                                ->nullable()
                                ->maxLength(255),

                            TextInput::make('container_class')
                                ->label(__('admin.fields.container_class'))
                                ->nullable()
                                ->maxLength(255),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('sort_order')
                                ->label(__('admin.fields.sort_order'))
                                ->numeric()
                                ->default(0),

                            Toggle::make('is_active')
                                ->label(__('admin.fields.is_active'))
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
