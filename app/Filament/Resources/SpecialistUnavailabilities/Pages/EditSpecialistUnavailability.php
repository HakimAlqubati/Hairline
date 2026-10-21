<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistUnavailabilities\Pages;

use App\Filament\Resources\SpecialistUnavailabilities\SpecialistUnavailabilityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpecialistUnavailability extends EditRecord
{
    protected static string $resource = SpecialistUnavailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
