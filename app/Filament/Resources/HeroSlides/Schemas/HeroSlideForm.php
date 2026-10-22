<?php

declare(strict_types=1);

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HeroSlideForm
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

                            TextInput::make('subtitle')
                                ->label(__('admin.fields.subtitle'))
                                ->nullable()
                                ->maxLength(255),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('badge_text')
                                ->label(__('admin.fields.badge_text'))
                                ->nullable()
                                ->maxLength(255),

                            TextInput::make('fallback_url')
                                ->label(__('admin.fields.fallback_url'))
                                ->url()
                                ->nullable(),
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
                            ->directory('hero')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
