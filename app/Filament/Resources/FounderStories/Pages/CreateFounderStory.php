<?php

declare(strict_types=1);

namespace App\Filament\Resources\FounderStories\Pages;

use App\Filament\Resources\FounderStories\FounderStoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFounderStory extends CreateRecord
{
    protected static string $resource = FounderStoryResource::class;
}
