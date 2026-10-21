<?php

declare(strict_types=1);

namespace App\Filament\Resources\CatalogPages\Pages;

use App\Filament\Resources\CatalogPages\CatalogPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCatalogPage extends EditRecord
{
    protected static string $resource = CatalogPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
