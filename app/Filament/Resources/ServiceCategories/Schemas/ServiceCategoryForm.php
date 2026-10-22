<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('admin.fields.name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('slug')
                                ->label(__('admin.fields.slug'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                        ]),

                        TextInput::make('subtitle')
                            ->label(__('admin.fields.subtitle'))
                            ->nullable()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label(__('admin.fields.description'))
                            ->rows(3)
                            ->nullable(),

                        Grid::make(3)->schema([
                            TextInput::make('icon')
                                ->label(__('admin.fields.icon'))
                                ->nullable()
                                ->maxLength(255),

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
