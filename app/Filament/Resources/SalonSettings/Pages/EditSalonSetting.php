<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonSettings\Pages;

use App\Filament\Resources\SalonSettings\SalonSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalonSetting extends EditRecord
{
    protected static string $resource = SalonSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
