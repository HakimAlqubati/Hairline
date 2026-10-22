<?php

declare(strict_types=1);

namespace App\Filament\Resources\FounderStories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FounderStoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('title')
                                ->label(__('admin.fields.title'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('founder_name')
                                ->label(__('admin.fields.founder_name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('founder_title')
                                ->label(__('admin.fields.founder_title'))
                                ->placeholder('المؤسسة والمديرة الإبداعية')
                                ->nullable()
                                ->maxLength(255),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('experience_badge')
                                ->label(__('admin.fields.experience_badge'))
                                ->placeholder('15+ عاماً من الشغف والتميز')
                                ->nullable()
                                ->maxLength(255),

                            TextInput::make('sort_order')
                                ->label(__('admin.fields.sort_order'))
                                ->numeric()
                                ->default(0),
                        ]),

                        Textarea::make('intro_text')
                            ->label(__('admin.fields.intro_text'))
                            ->rows(2)
                            ->nullable(),

                        RichEditor::make('description_text')
                            ->label(__('admin.fields.description_text'))
                            ->nullable(),

                        Textarea::make('vision_quote')
                            ->label(__('admin.fields.vision_quote'))
                            ->rows(2)
                            ->nullable(),

                        Grid::make(2)->schema([
                            FileUpload::make('portrait_image_path')
                                ->label(__('admin.fields.portrait_image_path'))
                                ->image()
                                ->directory('founder'),

                            FileUpload::make('signature_image_path')
                                ->label(__('admin.fields.signature_image_path'))
                                ->image()
                                ->directory('founder'),
                        ]),

                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
