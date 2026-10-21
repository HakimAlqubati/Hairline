<?php

declare(strict_types=1);

namespace App\Filament\Resources\CancellationPolicies;

use App\Filament\Resources\CancellationPolicies\Pages\CreateCancellationPolicy;
use App\Filament\Resources\CancellationPolicies\Pages\EditCancellationPolicy;
use App\Filament\Resources\CancellationPolicies\Pages\ListCancellationPolicies;
use App\Filament\Resources\CancellationPolicies\Schemas\CancellationPolicyForm;
use App\Filament\Resources\CancellationPolicies\Tables\CancellationPolicyTable;
use App\Models\CancellationPolicy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CancellationPolicyResource extends Resource
{
    protected static ?string $model = CancellationPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.website_content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.cancellation_policy');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.cancellation_policies');
    }

    public static function form(Schema $schema): Schema
    {
        return CancellationPolicyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CancellationPolicyTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCancellationPolicies::route('/'),
            'create' => CreateCancellationPolicy::route('/create'),
            'edit' => EditCancellationPolicy::route('/{record}/edit'),
        ];
    }
}
