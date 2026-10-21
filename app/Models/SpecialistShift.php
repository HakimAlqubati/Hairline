<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialistShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_day_off',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_day_off' => 'boolean',
        ];
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }
}
