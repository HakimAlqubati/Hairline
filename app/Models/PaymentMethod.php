<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PaymentMethod extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'fee',
        'sort_order',
        'is_available',
    ];

    public array $translatable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'float',
            'sort_order' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
