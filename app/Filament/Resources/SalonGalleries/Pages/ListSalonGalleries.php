<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonGalleries\Pages;

use App\Filament\Resources\SalonGalleries\SalonGalleryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalonGalleries extends ListRecords
{
    protected static string $resource = SalonGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
