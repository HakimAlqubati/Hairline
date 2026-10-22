<?php

declare(strict_types=1);

namespace App\Filament\Resources\MembershipTiers;

use App\Filament\Resources\MembershipTiers\Pages\CreateMembershipTier;
use App\Filament\Resources\MembershipTiers\Pages\EditMembershipTier;
use App\Filament\Resources\MembershipTiers\Pages\ListMembershipTiers;
use App\Filament\Resources\MembershipTiers\Schemas\MembershipTierForm;
use App\Filament\Resources\MembershipTiers\Tables\MembershipTierTable;
use App\Models\MembershipTier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MembershipTierResource extends Resource
{
    protected static ?string $model = MembershipTier::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.loyalty_program');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.membership_tier');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.membership_tiers');
    }

    public static function form(Schema $schema): Schema
    {
        return MembershipTierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembershipTierTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembershipTiers::route('/'),
            'create' => CreateMembershipTier::route('/create'),
            'edit' => EditMembershipTier::route('/{record}/edit'),
        ];
    }
}
