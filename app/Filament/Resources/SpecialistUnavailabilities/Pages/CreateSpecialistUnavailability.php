<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistUnavailabilities\Pages;

use App\Filament\Resources\SpecialistUnavailabilities\SpecialistUnavailabilityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialistUnavailability extends CreateRecord
{
    protected static string $resource = SpecialistUnavailabilityResource::class;
}
