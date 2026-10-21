<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceGroupTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('admin.fields.category'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('admin.fields.type'))
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('admin.filters.category'))
                    ->relationship('category', 'name'),

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
