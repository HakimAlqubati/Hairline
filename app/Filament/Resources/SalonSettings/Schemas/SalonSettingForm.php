<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonSettings\Schemas;

use App\Filament\Support\TranslatableField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalonSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->columnSpanFull()
                    ->schema([
                        TranslatableField::text('name', __('admin.fields.name'), required: true),

                        TranslatableField::text('tagline', __('admin.fields.tagline')),

                        TranslatableField::textarea('description_line1', __('admin.fields.description_line1'), rows: 2),

                        TranslatableField::textarea('description_line2', __('admin.fields.description_line2'), rows: 2),

                        TranslatableField::textarea('address', __('admin.fields.address'), rows: 2),

                        FileUpload::make('logo_path')
                            ->label(__('admin.fields.logo_path'))
                            ->image()
                            ->directory('settings')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('admin.sections.contact_and_social'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('phone')
                                ->label(__('admin.fields.phone'))
                                ->tel()
                                ->nullable(),

                            TextInput::make('whatsapp')
                                ->label(__('admin.fields.whatsapp'))
                                ->tel()
                                ->nullable(),

                            TextInput::make('email')
                                ->label(__('admin.fields.email'))
                                ->email()
                                ->nullable(),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('instagram')
                                ->label(__('admin.fields.instagram'))
                                ->nullable(),

                            TextInput::make('tiktok')
                                ->label(__('admin.fields.tiktok'))
                                ->nullable(),

                            TextInput::make('snapchat')
                                ->label(__('admin.fields.snapchat'))
                                ->nullable(),
                        ]),
                    ]),

                Section::make(__('admin.sections.location_and_tax'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('latitude')
                                ->label(__('admin.fields.latitude'))
                                ->numeric()
                                ->nullable(),

                            TextInput::make('longitude')
                                ->label(__('admin.fields.longitude'))
                                ->numeric()
                                ->nullable(),

                            TextInput::make('google_maps_url')
                                ->label(__('admin.fields.google_maps_url'))
                                ->url()
                                ->nullable(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('tax_number')
                                ->label(__('admin.fields.tax_number'))
                                ->nullable(),

                            TextInput::make('vat_percentage')
                                ->label(__('admin.fields.vat_percentage'))
                                ->numeric()
                                ->suffix('%')
                                ->default(15.00),
                        ]),

                        KeyValue::make('stats')
                            ->label(__('admin.fields.stats'))
                            ->nullable(),
                    ]),
            ]);
    }
}
