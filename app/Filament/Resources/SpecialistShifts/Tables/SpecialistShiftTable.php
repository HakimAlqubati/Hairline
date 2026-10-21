<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistShifts\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SpecialistShiftTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('specialist.name')
                    ->label(__('admin.fields.specialist'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('day_of_week')
                    ->label(__('admin.fields.day_of_week'))
                    ->formatStateUsing(fn ($state) => __('admin.options.days')[$state] ?? $state)
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label(__('admin.fields.start_time'))
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label(__('admin.fields.end_time'))
                    ->time('H:i')
                    ->sortable(),

                IconColumn::make('is_day_off')
                    ->label(__('admin.fields.is_day_off'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('specialist_id')
                    ->label(__('admin.filters.specialist'))
                    ->relationship('specialist', 'name'),

                SelectFilter::make('day_of_week')
                    ->label(__('admin.fields.day_of_week'))
                    ->options(__('admin.options.days')),

                TernaryFilter::make('is_day_off')
                    ->label(__('admin.fields.is_day_off')),
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
