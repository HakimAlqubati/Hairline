<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FounderStory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title',
        'founder_name',
        'founder_title',
        'intro_text',
        'description_text',
        'belief_text',
        'founder_message_title',
        'founder_message_paragraphs',
        'founding_year',
        'founder_image_path',
        'is_active',
    ];

    public array $translatable = [
        'title',
        'founder_name',
        'founder_title',
        'intro_text',
        'description_text',
        'belief_text',
        'founder_message_title',
        'founder_message_paragraphs',
    ];

    protected function casts(): array
    {
        return [
            'founding_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
