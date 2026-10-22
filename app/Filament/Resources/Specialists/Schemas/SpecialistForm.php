<?php

declare(strict_types=1);

namespace App\Filament\Resources\Specialists\Schemas;

use App\Filament\Support\TranslatableField;
use Filament\Forms\Components\FileUpload;
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
                    ->columnSpanFull()
                    ->schema([
                        TranslatableField::text('name', __('admin.fields.name'), required: true),

                        Grid::make(2)->schema([
                            TextInput::make('slug')
                                ->label(__('admin.fields.slug'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

                            TextInput::make('phone')
                                ->label(__('admin.fields.phone'))
                                ->tel()
                                ->nullable()
                                ->maxLength(255),
                        ]),

                        TranslatableField::text('role', __('admin.fields.role'), required: true),

                        TranslatableField::text('experience', __('admin.fields.experience')),

                        Select::make('services')
                            ->label(__('admin.resources.services'))
                            ->relationship('services', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),

                        TranslatableField::richEditor('bio', __('admin.fields.bio')),
                    ]),

                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('is_home_specialist')
                                ->label(__('admin.fields.is_home_specialist'))
                                ->default(false)
                                ->hidden(),

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
