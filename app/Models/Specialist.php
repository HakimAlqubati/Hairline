<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Specialist extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'name',
        'role',
        'experience',
        'bio',
        'avatar_url',
        'phone',
        'is_home_specialist',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'name',
        'role',
        'experience',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'is_home_specialist' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'specialist_service')
            ->withPivot('custom_duration_minutes')
            ->withTimestamps();
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(SpecialistShift::class);
    }

    public function unavailabilities(): HasMany
    {
        return $this->hasMany(SpecialistUnavailability::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
}
