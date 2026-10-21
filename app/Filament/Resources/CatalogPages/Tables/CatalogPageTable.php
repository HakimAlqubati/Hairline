<?php

declare(strict_types=1);

namespace App\Filament\Resources\CatalogPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CatalogPageTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page_number')
                    ->label(__('admin.fields.page_number'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('layout_type')
                    ->label(__('admin.fields.layout_type'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('page_number', 'asc')
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
