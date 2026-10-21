<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistShifts\Pages;

use App\Filament\Resources\SpecialistShifts\SpecialistShiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpecialistShifts extends ListRecords
{
    protected static string $resource = SpecialistShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
