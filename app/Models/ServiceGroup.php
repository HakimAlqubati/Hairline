<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ServiceGroup extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'category_id',
        'name',
        'type',
        'container_class',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'group_id')->orderBy('sort_order');
    }
}
