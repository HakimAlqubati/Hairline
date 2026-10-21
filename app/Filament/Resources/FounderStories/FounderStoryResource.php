<?php

declare(strict_types=1);

namespace App\Filament\Resources\FounderStories;

use App\Filament\Resources\FounderStories\Pages\CreateFounderStory;
use App\Filament\Resources\FounderStories\Pages\EditFounderStory;
use App\Filament\Resources\FounderStories\Pages\ListFounderStories;
use App\Filament\Resources\FounderStories\Schemas\FounderStoryForm;
use App\Filament\Resources\FounderStories\Tables\FounderStoryTable;
use App\Models\FounderStory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FounderStoryResource extends Resource
{
    protected static ?string $model = FounderStory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.website_content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.founder_story');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.founder_stories');
    }

    public static function form(Schema $schema): Schema
    {
        return FounderStoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FounderStoryTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFounderStories::route('/'),
            'create' => CreateFounderStory::route('/create'),
            'edit' => EditFounderStory::route('/{record}/edit'),
        ];
    }
}
