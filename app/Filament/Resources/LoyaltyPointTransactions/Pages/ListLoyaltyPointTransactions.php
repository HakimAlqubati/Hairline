<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyPointTransactions\Pages;

use App\Filament\Resources\LoyaltyPointTransactions\LoyaltyPointTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyPointTransactions extends ListRecords
{
    protected static string $resource = LoyaltyPointTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
