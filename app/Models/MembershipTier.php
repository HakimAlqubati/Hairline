<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class MembershipTier extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'name',
        'benefits_summary',
        'badge_color',
        'min_points_required',
        'discount_percentage',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'name',
        'benefits_summary',
    ];

    protected function casts(): array
    {
        return [
            'min_points_required' => 'integer',
            'discount_percentage' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
