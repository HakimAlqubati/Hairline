<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

class TranslatableField
{
    public static function text(string $name, string $label, bool $required = false): Grid
    {
        return Grid::make(2)->schema([
            TextInput::make("{$name}.ar")
                ->label("{$label} (العربية)")
                ->required($required)
                ->maxLength(255),

            TextInput::make("{$name}.en")
                ->label("{$label} (English)")
                ->required($required)
                ->maxLength(255),
        ]);
    }

    public static function textarea(string $name, string $label, bool $required = false, int $rows = 3): Grid
    {
        return Grid::make(2)->schema([
            Textarea::make("{$name}.ar")
                ->label("{$label} (العربية)")
                ->required($required)
                ->rows($rows),

            Textarea::make("{$name}.en")
                ->label("{$label} (English)")
                ->required($required)
                ->rows($rows),
        ]);
    }

    public static function richEditor(string $name, string $label, bool $required = false): Grid
    {
        return Grid::make(2)->schema([
            RichEditor::make("{$name}.ar")
                ->label("{$label} (العربية)")
                ->required($required),

            RichEditor::make("{$name}.en")
                ->label("{$label} (English)")
                ->required($required),
        ]);
    }
}
