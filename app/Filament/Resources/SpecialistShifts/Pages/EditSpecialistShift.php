<?php

declare(strict_types=1);

namespace App\Filament\Resources\SpecialistShifts\Pages;

use App\Filament\Resources\SpecialistShifts\SpecialistShiftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpecialistShift extends EditRecord
{
    protected static string $resource = SpecialistShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
