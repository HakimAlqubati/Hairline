<?php

declare(strict_types=1);

namespace App\Filament\Resources\HairLengths\Pages;

use App\Filament\Resources\HairLengths\HairLengthResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHairLength extends CreateRecord
{
    protected static string $resource = HairLengthResource::class;
}
