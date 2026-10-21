<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonWorkingHours\Pages;

use App\Filament\Resources\SalonWorkingHours\SalonWorkingHourResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalonWorkingHour extends CreateRecord
{
    protected static string $resource = SalonWorkingHourResource::class;
}
