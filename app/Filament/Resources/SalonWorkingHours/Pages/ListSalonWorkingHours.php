<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonWorkingHours\Pages;

use App\Filament\Resources\SalonWorkingHours\SalonWorkingHourResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalonWorkingHours extends ListRecords
{
    protected static string $resource = SalonWorkingHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
