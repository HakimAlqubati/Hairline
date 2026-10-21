<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceGroups\Pages;

use App\Filament\Resources\ServiceGroups\ServiceGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceGroup extends CreateRecord
{
    protected static string $resource = ServiceGroupResource::class;
}
