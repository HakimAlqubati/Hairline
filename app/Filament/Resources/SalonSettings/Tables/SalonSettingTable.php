<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonSettings\Tables;

use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalonSettingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label(__('admin.fields.logo_path')),

                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label(__('admin.fields.phone')),

                TextColumn::make('email')
                    ->label(__('admin.fields.email')),

                TextColumn::make('vat_percentage')
                    ->label(__('admin.fields.vat_percentage'))
                    ->suffix('%'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
