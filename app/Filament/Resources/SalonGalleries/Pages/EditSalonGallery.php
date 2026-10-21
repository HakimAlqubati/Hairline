<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonGalleries\Pages;

use App\Filament\Resources\SalonGalleries\SalonGalleryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalonGallery extends EditRecord
{
    protected static string $resource = SalonGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
