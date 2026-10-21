<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SalonWorkingHour extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'day_of_week',
        'day_label',
        'opening_time',
        'closing_time',
        'is_closed',
        'notes',
    ];

    public array $translatable = [
        'day_label',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_closed' => 'boolean',
        ];
    }
}
