<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistUnavailabilities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SpecialistUnavailabilityTable
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

                TextColumn::make('start_datetime')
                    ->label(__('admin.fields.start_datetime'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('end_datetime')
                    ->label(__('admin.fields.end_datetime'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label(__('admin.fields.reason'))
                    ->limit(40)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('specialist_id')
                    ->label(__('admin.filters.specialist'))
                    ->relationship('specialist', 'name'),
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
