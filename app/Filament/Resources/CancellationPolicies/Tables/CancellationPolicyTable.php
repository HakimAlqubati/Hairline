<?php

declare(strict_types=1);

namespace App\Filament\Resources\CancellationPolicies\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CancellationPolicyTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('free_cancellation_hours')
                    ->label(__('admin.fields.free_cancellation_hours'))
                    ->suffix(' '.__('admin.units.hours'))
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('partial_refund_hours')
                    ->label(__('admin.fields.partial_refund_hours'))
                    ->suffix(' '.__('admin.units.hours'))
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('partial_refund_percentage')
                    ->label(__('admin.fields.partial_refund_percentage'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('no_show_penalty_percentage')
                    ->label(__('admin.fields.no_show_penalty_percentage'))
                    ->suffix('%')
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('admin.filters.active_status')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
