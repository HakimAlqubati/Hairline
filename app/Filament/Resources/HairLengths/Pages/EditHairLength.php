<?php

declare(strict_types=1);

namespace App\Filament\Resources\HairLengths\Pages;

use App\Filament\Resources\HairLengths\HairLengthResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHairLength extends EditRecord
{
    protected static string $resource = HairLengthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
