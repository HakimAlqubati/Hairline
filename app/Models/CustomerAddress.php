<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class CustomerAddress extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'customer_id',
        'district_id',
        'street_name',
        'building_number',
        'floor_number',
        'notes',
        'latitude',
        'longitude',
        'is_default',
    ];

    public array $translatable = [
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_default' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
