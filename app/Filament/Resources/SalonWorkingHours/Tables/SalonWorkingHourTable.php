<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonWorkingHours\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SalonWorkingHourTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_of_week')
                    ->label(__('admin.fields.day_of_week'))
                    ->formatStateUsing(fn ($state) => __('admin.options.days')[$state] ?? $state)
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('day_label')
                    ->label(__('admin.fields.day_label')),

                TextColumn::make('open_time')
                    ->label(__('admin.fields.open_time'))
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('close_time')
                    ->label(__('admin.fields.close_time'))
                    ->time('H:i')
                    ->sortable(),

                IconColumn::make('is_closed')
                    ->label(__('admin.fields.is_closed'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('day_of_week', 'asc')
            ->filters([
                TernaryFilter::make('is_closed')
                    ->label(__('admin.fields.is_closed')),
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
