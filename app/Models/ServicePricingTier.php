<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ServicePricingTier extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'service_id',
        'hair_length_id',
        'custom_label',
        'price',
        'extra_duration_minutes',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'custom_label',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'extra_duration_minutes' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function hairLength(): BelongsTo
    {
        return $this->belongsTo(HairLength::class);
    }
}
