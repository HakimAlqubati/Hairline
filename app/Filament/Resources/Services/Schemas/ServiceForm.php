<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\TranslatableField;
use Filament\Forms\Components\FileUpload;
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
                    ->columnSpanFull()
                    ->schema([
                        TranslatableField::text('name', __('admin.fields.name'), required: true),

                        Grid::make(3)->schema([
                            TextInput::make('code')
                                ->label(__('admin.fields.code'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

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

                        TranslatableField::text('price_display_override', __('admin.fields.price_display_override')),

                        TranslatableField::richEditor('inclusions', __('admin.fields.inclusions')),

                        TranslatableField::richEditor('description', __('admin.fields.description')),
                    ]),

                Section::make(__('admin.sections.pricing_and_duration'))
                    ->columnSpanFull()
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

                            Toggle::make('is_home_available')
                                ->label(__('admin.fields.is_home_available'))
                                ->default(false)
                                ->hidden(),
                        ]),
                    ]),

                Section::make(__('admin.sections.media'))
                    ->columnSpanFull()
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
