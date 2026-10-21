<?php

declare(strict_types=1);

namespace App\Filament\Resources\CatalogPages\Pages;

use App\Filament\Resources\CatalogPages\CatalogPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCatalogPage extends CreateRecord
{
    protected static string $resource = CatalogPageResource::class;
}
