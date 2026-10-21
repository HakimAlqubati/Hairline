<?php

declare(strict_types=1);

namespace App\Filament\Resources\FounderStories\Pages;

use App\Filament\Resources\FounderStories\FounderStoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFounderStories extends ListRecords
{
    protected static string $resource = FounderStoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
