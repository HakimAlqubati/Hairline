<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label(__('admin.fields.image'))
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('admin.fields.category'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('base_price')
                    ->label(__('admin.fields.base_price'))
                    ->money('SAR')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label(__('admin.fields.duration_minutes'))
                    ->suffix(' '.__('admin.units.minutes'))
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label(__('admin.fields.is_featured'))
                    ->boolean()
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

                TernaryFilter::make('is_featured')
                    ->label(__('admin.filters.featured_status')),
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
