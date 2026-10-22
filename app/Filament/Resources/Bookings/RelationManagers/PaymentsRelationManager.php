<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bookings\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.payments');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Select::make('payment_method_id')
                        ->label(__('admin.fields.payment_method'))
                        ->relationship('paymentMethod', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('amount')
                        ->label(__('admin.fields.amount'))
                        ->numeric()
                        ->prefix('SAR')
                        ->required(),

                    Select::make('payment_type')
                        ->label(__('admin.fields.payment_type'))
                        ->options([
                            'deposit' => __('admin.options.payment_type_deposit'),
                            'final' => __('admin.options.payment_type_final'),
                            'refund' => __('admin.options.payment_type_refund'),
                        ])
                        ->default('deposit')
                        ->required(),
                ]),

                Grid::make(3)->schema([
                    Select::make('status')
                        ->label(__('admin.fields.status'))
                        ->options([
                            'success' => __('admin.statuses.success'),
                            'pending' => __('admin.statuses.pending'),
                            'failed' => __('admin.statuses.failed'),
                        ])
                        ->default('success')
                        ->required(),

                    TextInput::make('transaction_reference')
                        ->label(__('admin.fields.transaction_reference'))
                        ->nullable(),

                    DateTimePicker::make('paid_at')
                        ->label(__('admin.fields.paid_at'))
                        ->default(now()),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transaction_reference')
            ->columns([
                TextColumn::make('paymentMethod.name')
                    ->label(__('admin.fields.payment_method'))
                    ->badge()
                    ->color('info'),

                TextColumn::make('amount')
                    ->label(__('admin.fields.amount'))
                    ->money('SAR'),

                TextColumn::make('payment_type')
                    ->label(__('admin.fields.payment_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('admin.options.payment_type_'.$state) ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'final' => 'success',
                        'deposit' => 'warning',
                        'refund' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('admin.statuses.'.$state) ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('paid_at')
                    ->label(__('admin.fields.paid_at'))
                    ->dateTime('Y-m-d H:i'),
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
