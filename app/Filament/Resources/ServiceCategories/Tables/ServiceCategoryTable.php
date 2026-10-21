<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceCategoryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->toggleable(),

                TextColumn::make('services_count')
                    ->label(__('admin.resources.services'))
                    ->counts('services')
                    ->badge()
                    ->color('info')
                    ->sortable(),

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
