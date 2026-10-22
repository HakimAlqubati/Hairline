<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.service_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('admin.fields.name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('code')
                                ->label(__('admin.fields.code'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('category_id')
                                ->label(__('admin.fields.category'))
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('group_id')
                                ->label(__('admin.fields.service_group'))
                                ->relationship('serviceGroup', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),

                        TextInput::make('price_display_override')
                            ->label(__('admin.fields.price_display_override'))
                            ->helperText(__('admin.helpers.price_display_override'))
                            ->nullable(),

                        RichEditor::make('inclusions')
                            ->label(__('admin.fields.inclusions'))
                            ->nullable(),

                        RichEditor::make('description')
                            ->label(__('admin.fields.description'))
                            ->nullable(),
                    ]),

                Section::make(__('admin.sections.pricing_and_duration'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('base_price')
                                ->label(__('admin.fields.base_price'))
                                ->numeric()
                                ->prefix('SAR')
                                ->required(),

                            TextInput::make('duration_minutes')
                                ->label(__('admin.fields.duration_minutes'))
                                ->numeric()
                                ->suffix(__('admin.units.minutes'))
                                ->required(),

                            TextInput::make('sort_order')
                                ->label(__('admin.fields.sort_order'))
                                ->numeric()
                                ->default(0),
                        ]),

                        Grid::make(2)->schema([
                            Toggle::make('is_active')
                                ->label(__('admin.fields.is_active'))
                                ->default(true),

                            Toggle::make('is_featured')
                                ->label(__('admin.fields.is_featured'))
                                ->default(false),
                        ]),
                    ]),

                Section::make(__('admin.sections.media'))
                    ->schema([
                        FileUpload::make('image_path')
                            ->label(__('admin.fields.image'))
                            ->image()
                            ->directory('services')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
