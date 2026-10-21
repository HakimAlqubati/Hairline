<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bookings\RelationManagers;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BookingItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.booking_items');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Select::make('service_id')
                        ->label(__('admin.fields.service'))
                        ->relationship('service', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('specialist_id')
                        ->label(__('admin.fields.specialist'))
                        ->relationship('specialist', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('hair_length_id')
                        ->label(__('admin.fields.hair_length'))
                        ->relationship('hairLength', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),

                Grid::make(4)->schema([
                    TextInput::make('unit_price')
                        ->label(__('admin.fields.unit_price'))
                        ->numeric()
                        ->prefix('SAR')
                        ->required(),

                    TextInput::make('duration_minutes')
                        ->label(__('admin.fields.duration_minutes'))
                        ->numeric()
                        ->suffix(__('admin.units.minutes'))
                        ->required(),

                    TimePicker::make('scheduled_start_time')
                        ->label(__('admin.fields.scheduled_start_time'))
                        ->required(),

                    TimePicker::make('scheduled_end_time')
                        ->label(__('admin.fields.scheduled_end_time'))
                        ->required(),
                ]),

                Select::make('status')
                    ->label(__('admin.fields.status'))
                    ->options([
                        'pending' => __('admin.statuses.pending'),
                        'confirmed' => __('admin.statuses.confirmed'),
                        'in_progress' => __('admin.statuses.in_progress'),
                        'completed' => __('admin.statuses.completed'),
                        'cancelled' => __('admin.statuses.cancelled'),
                    ])
                    ->default('confirmed')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('service.name')
            ->columns([
                TextColumn::make('service.name')
                    ->label(__('admin.fields.service'))
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('specialist.name')
                    ->label(__('admin.fields.specialist'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('hairLength.name')
                    ->label(__('admin.fields.hair_length'))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('unit_price')
                    ->label(__('admin.fields.unit_price'))
                    ->money('SAR'),

                TextColumn::make('duration_minutes')
                    ->label(__('admin.fields.duration_minutes'))
                    ->suffix(' '.__('admin.units.minutes')),

                TextColumn::make('scheduled_start_time')
                    ->label(__('admin.fields.scheduled_start_time'))
                    ->time('H:i'),

                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('admin.statuses.'.$state) ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'completed' => 'success',
                        'confirmed' => 'info',
                        'in_progress' => 'primary',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
