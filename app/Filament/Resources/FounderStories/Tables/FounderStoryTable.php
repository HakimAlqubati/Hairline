<?php

declare(strict_types=1);

namespace App\Filament\Resources\FounderStories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FounderStoryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('portrait_image_path')
                    ->label(__('admin.fields.portrait_image_path'))
                    ->circular(),

                TextColumn::make('founder_name')
                    ->label(__('admin.fields.founder_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('founder_title')
                    ->label(__('admin.fields.founder_title')),

                TextColumn::make('experience_badge')
                    ->label(__('admin.fields.experience_badge'))
                    ->badge()
                    ->color('warning'),

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
