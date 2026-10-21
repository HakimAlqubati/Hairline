<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SalonSetting extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'tagline',
        'description_line1',
        'description_line2',
        'address',
        'logo_path',
        'phone',
        'whatsapp',
        'instagram',
        'tiktok',
        'snapchat',
        'email',
        'latitude',
        'longitude',
        'google_maps_url',
        'tax_number',
        'vat_percentage',
        'stats',
    ];

    public array $translatable = [
        'name',
        'tagline',
        'description_line1',
        'description_line2',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'vat_percentage' => 'float',
            'stats' => 'array',
        ];
    }
}
