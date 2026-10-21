<?php

declare(strict_types=1);

namespace App\Filament\Resources\CatalogPages\Pages;

use App\Filament\Resources\CatalogPages\CatalogPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCatalogPages extends ListRecords
{
    protected static string $resource = CatalogPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
