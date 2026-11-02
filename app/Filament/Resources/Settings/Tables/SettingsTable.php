<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('admin.fields.id') ?? 'المفتاح')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label(__('admin.fields.notes') ?? 'القيمة')
                    ->limit(60)
                    ->searchable(),
            ]);
    }
}
