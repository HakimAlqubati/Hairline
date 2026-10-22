<?php

declare(strict_types=1);

namespace App\Filament\Resources\CatalogPages\Schemas;

use App\Filament\Support\TranslatableField;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CatalogPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        TranslatableField::text('title', __('admin.fields.title'), required: true),

                        Grid::make(2)->schema([
                            TextInput::make('page_number')
                                ->label(__('admin.fields.page_number'))
                                ->numeric()
                                ->required(),

                            Select::make('layout_type')
                                ->label(__('admin.fields.layout_type'))
                                ->options([
                                    'single' => 'صفحة أحادية (Single)',
                                    'spread' => 'صفحتين متقابلتين (Spread)',
                                    'grid' => 'شبكة عناصر (Grid)',
                                    'cover' => 'غلاف (Cover)',
                                ])
                                ->default('single')
                                ->required(),
                        ]),

                        KeyValue::make('meta')
                            ->label(__('admin.fields.meta'))
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
