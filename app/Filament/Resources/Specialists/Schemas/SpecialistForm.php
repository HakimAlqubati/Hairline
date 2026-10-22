<?php

declare(strict_types=1);

namespace App\Filament\Resources\Specialists\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SpecialistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.specialist_profile'))
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

                        Grid::make(3)->schema([
                            TextInput::make('role')
                                ->label(__('admin.fields.role'))
                                ->placeholder('أخصائية صبغات ومكياج')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('experience')
                                ->label(__('admin.fields.experience'))
                                ->placeholder('7+ سنوات خبرة')
                                ->nullable()
                                ->maxLength(255),

                            TextInput::make('phone')
                                ->label(__('admin.fields.phone'))
                                ->tel()
                                ->nullable()
                                ->maxLength(255),
                        ]),

                        Select::make('services')
                            ->label(__('admin.resources.services'))
                            ->relationship('services', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),

                        RichEditor::make('bio')
                            ->label(__('admin.fields.bio'))
                            ->nullable(),
                    ]),

                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('is_home_specialist')
                                ->label(__('admin.fields.is_home_specialist'))
                                ->default(false),

                            Toggle::make('is_active')
                                ->label(__('admin.fields.is_active'))
                                ->default(true),

                            TextInput::make('sort_order')
                                ->label(__('admin.fields.sort_order'))
                                ->numeric()
                                ->default(0),
                        ]),

                        FileUpload::make('avatar_url')
                            ->label(__('admin.fields.avatar'))
                            ->image()
                            ->directory('specialists')
                            ->avatar()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
