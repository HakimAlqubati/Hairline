<?php

declare(strict_types=1);

namespace App\Filament\Resources\Specialists\Pages;

use App\Filament\Resources\Specialists\SpecialistResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialist extends CreateRecord
{
    protected static string $resource = SpecialistResource::class;
}
