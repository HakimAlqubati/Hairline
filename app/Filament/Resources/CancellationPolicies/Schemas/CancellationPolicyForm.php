<?php

declare(strict_types=1);

namespace App\Filament\Resources\CancellationPolicies\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CancellationPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.general_information'))
                    ->schema([
                        RichEditor::make('policy_text')
                            ->label(__('admin.fields.policy_text'))
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            TextInput::make('free_cancellation_hours')
                                ->label(__('admin.fields.free_cancellation_hours'))
                                ->numeric()
                                ->suffix(__('admin.units.hours'))
                                ->default(24)
                                ->required(),

                            TextInput::make('partial_refund_hours')
                                ->label(__('admin.fields.partial_refund_hours'))
                                ->numeric()
                                ->suffix(__('admin.units.hours'))
                                ->default(12)
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('partial_refund_percentage')
                                ->label(__('admin.fields.partial_refund_percentage'))
                                ->numeric()
                                ->suffix('%')
                                ->default(50)
                                ->required(),

                            TextInput::make('no_show_penalty_percentage')
                                ->label(__('admin.fields.no_show_penalty_percentage'))
                                ->numeric()
                                ->suffix('%')
                                ->default(100)
                                ->required(),
                        ]),

                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
