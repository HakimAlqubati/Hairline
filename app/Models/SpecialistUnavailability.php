<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class SpecialistUnavailability extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'specialist_id',
        'start_datetime',
        'end_datetime',
        'reason',
    ];

    public array $translatable = [
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
        ];
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }
}
