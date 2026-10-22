<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.customer_info'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('customer_code')
                                ->label(__('admin.fields.customer_code'))
                                ->default(fn () => 'CUS-'.strtoupper(bin2hex(random_bytes(3))))
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('name')
                                ->label(__('admin.fields.name'))
                                ->required()
                                ->maxLength(255),

                            Select::make('membership_tier_id')
                                ->label(__('admin.fields.membership_tier'))
                                ->relationship('membershipTier', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('phone')
                                ->label(__('admin.fields.phone'))
                                ->tel()
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('email')
                                ->label(__('admin.fields.email'))
                                ->email()
                                ->nullable(),

                            TextInput::make('total_points')
                                ->label(__('admin.fields.total_points'))
                                ->numeric()
                                ->default(0),
                        ]),

                        Grid::make(2)->schema([
                            Toggle::make('is_active')
                                ->label(__('admin.fields.is_active'))
                                ->default(true),

                            FileUpload::make('avatar_url')
                                ->label(__('admin.fields.avatar'))
                                ->image()
                                ->avatar()
                                ->directory('customers'),
                        ]),
                    ]),
            ]);
    }
}
