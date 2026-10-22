<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyPointTransactions;

use App\Filament\Resources\LoyaltyPointTransactions\Pages\CreateLoyaltyPointTransaction;
use App\Filament\Resources\LoyaltyPointTransactions\Pages\ListLoyaltyPointTransactions;
use App\Filament\Resources\LoyaltyPointTransactions\Schemas\LoyaltyPointTransactionForm;
use App\Filament\Resources\LoyaltyPointTransactions\Tables\LoyaltyPointTransactionTable;
use App\Models\LoyaltyPointTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LoyaltyPointTransactionResource extends Resource
{
    protected static ?string $model = LoyaltyPointTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.loyalty_program');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.loyalty_point_transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.loyalty_point_transactions');
    }

    public static function form(Schema $schema): Schema
    {
        return LoyaltyPointTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoyaltyPointTransactionTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyPointTransactions::route('/'),
            'create' => CreateLoyaltyPointTransaction::route('/create'),
        ];
    }
}
