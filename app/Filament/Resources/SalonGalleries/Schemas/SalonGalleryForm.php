<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonGalleries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalonGalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label(__('admin.fields.title'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('slug')
                                ->label(__('admin.fields.slug'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('subtitle')
                                ->label(__('admin.fields.subtitle'))
                                ->nullable()
                                ->maxLength(255),

                            TextInput::make('tag')
                                ->label(__('admin.fields.tag'))
                                ->placeholder('تسريحات، صبغات، عناية')
                                ->nullable()
                                ->maxLength(255),

                            Select::make('aspect_ratio')
                                ->label(__('admin.fields.aspect_ratio'))
                                ->options([
                                    '16/10' => '16:10 (عريض)',
                                    '1/1' => '1:1 (مربع)',
                                    '4/5' => '4:5 (بورتريه إنستغرام)',
                                    '16/9' => '16:9 (سينمائي)',
                                ])
                                ->default('16/10'),
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

                        FileUpload::make('image_path')
                            ->label(__('admin.fields.image'))
                            ->image()
                            ->directory('gallery')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
