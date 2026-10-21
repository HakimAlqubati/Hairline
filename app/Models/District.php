<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class District extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'name',
        'zone',
        'extra_fee',
        'is_available',
    ];

    public array $translatable = [
        'name',
        'zone',
    ];

    protected function casts(): array
    {
        return [
            'extra_fee' => 'float',
            'is_available' => 'boolean',
        ];
    }

    public function customerAddresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }
}
