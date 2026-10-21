<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class HairLength extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'code',
        'name',
        'description',
        'approx_length_cm',
        'diagram_order',
        'is_active',
    ];

    public array $translatable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'approx_length_cm' => 'integer',
            'diagram_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(ServicePricingTier::class);
    }
}
