<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistShifts\Pages;

use App\Filament\Resources\SpecialistShifts\SpecialistShiftResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialistShift extends CreateRecord
{
    protected static string $resource = SpecialistShiftResource::class;
}
