<?php

declare(strict_types=1);

namespace App\Filament\Resources\HairLengths\Pages;

use App\Filament\Resources\HairLengths\HairLengthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHairLengths extends ListRecords
{
    protected static string $resource = HairLengthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
