<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyPointTransactions\Pages;

use App\Filament\Resources\LoyaltyPointTransactions\LoyaltyPointTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoyaltyPointTransaction extends CreateRecord
{
    protected static string $resource = LoyaltyPointTransactionResource::class;
}
