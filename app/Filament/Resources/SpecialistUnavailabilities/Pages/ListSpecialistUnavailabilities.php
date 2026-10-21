<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistUnavailabilities\Pages;

use App\Filament\Resources\SpecialistUnavailabilities\SpecialistUnavailabilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpecialistUnavailabilities extends ListRecords
{
    protected static string $resource = SpecialistUnavailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
