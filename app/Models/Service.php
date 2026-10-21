<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'category_id',
        'group_id',
        'code',
        'name',
        'inclusions',
        'description',
        'price_display_override',
        'base_price',
        'duration_minutes',
        'buffer_minutes',
        'has_length_pricing',
        'is_popular',
        'is_home_available',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'name',
        'inclusions',
        'description',
        'price_display_override',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'float',
            'duration_minutes' => 'integer',
            'buffer_minutes' => 'integer',
            'has_length_pricing' => 'boolean',
            'is_popular' => 'boolean',
            'is_home_available' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class, 'group_id');
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(ServicePricingTier::class)->orderBy('sort_order');
    }

    public function specialists(): BelongsToMany
    {
        return $this->belongsToMany(Specialist::class, 'specialist_service')
            ->withPivot('custom_duration_minutes')
            ->withTimestamps();
    }
}
