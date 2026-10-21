<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ServiceCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'catalog_page_id',
        'slug',
        'name',
        'subtitle',
        'icon',
        'page_ref',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'name',
        'subtitle',
    ];

    protected function casts(): array
    {
        return [
            'page_ref' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function catalogPage(): BelongsTo
    {
        return $this->belongsTo(CatalogPage::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(ServiceGroup::class, 'category_id')->orderBy('sort_order');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id')->orderBy('sort_order');
    }
}
