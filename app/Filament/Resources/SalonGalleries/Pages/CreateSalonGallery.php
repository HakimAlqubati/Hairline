<?php

declare(strict_types=1);

namespace App\Filament\Resources\SalonGalleries\Pages;

use App\Filament\Resources\SalonGalleries\SalonGalleryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalonGallery extends CreateRecord
{
    protected static string $resource = SalonGalleryResource::class;
}
