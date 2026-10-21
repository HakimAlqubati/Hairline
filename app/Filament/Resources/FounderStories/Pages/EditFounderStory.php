<?php

declare(strict_types=1);

namespace App\Filament\Resources\FounderStories\Pages;

use App\Filament\Resources\FounderStories\FounderStoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFounderStory extends EditRecord
{
    protected static string $resource = FounderStoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
