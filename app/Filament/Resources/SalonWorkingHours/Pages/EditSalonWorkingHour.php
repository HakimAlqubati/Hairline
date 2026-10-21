<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonWorkingHours\Pages;

use App\Filament\Resources\SalonWorkingHours\SalonWorkingHourResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalonWorkingHour extends EditRecord
{
    protected static string $resource = SalonWorkingHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
