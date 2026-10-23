<?php

declare(strict_types=1);

namespace App\Filament\Resources\Specialists\Tables;

use App\Models\Specialist;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SpecialistTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('admin.fields.avatar'))
                    ->circular()
                    ->state(function (Specialist $record): ?string {
                        $avatar = $record->avatar_url;
                        if (! $avatar) {
                            return null;
                        }
                        if (str_starts_with($avatar, 'http') || str_starts_with($avatar, 'data:')) {
                            return $avatar;
                        }
                        if (str_starts_with($avatar, 'images/') || str_starts_with($avatar, '/images/')) {
                            return asset(ltrim($avatar, '/'));
                        }

                        return asset('storage/'.ltrim($avatar, '/'));
                    }),

                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('role')
                    ->label(__('admin.fields.role'))
                    ->searchable(),

                TextColumn::make('experience')
                    ->label(__('admin.fields.experience')),

                TextColumn::make('phone')
                    ->label(__('admin.fields.phone'))
                    ->searchable(),

                IconColumn::make('is_home_specialist')
                    ->label(__('admin.fields.is_home_specialist'))
                    ->boolean()
                    ->sortable()
                    ->hidden(),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_home_specialist')
                    ->label(__('admin.fields.is_home_specialist'))
                    ->hidden(),

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
