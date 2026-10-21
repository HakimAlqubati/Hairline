<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class CatalogPage extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'page_number',
        'title',
        'layout_type',
        'meta',
        'is_active',
    ];

    public array $translatable = [
        'title',
    ];

    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
            'meta' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ServiceCategory::class)->orderBy('sort_order');
    }
}
