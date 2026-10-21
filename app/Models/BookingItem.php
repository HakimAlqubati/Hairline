<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'service_id',
        'specialist_id',
        'hair_length_id',
        'unit_price',
        'duration_minutes',
        'scheduled_start_time',
        'scheduled_end_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'float',
            'duration_minutes' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    public function hairLength(): BelongsTo
    {
        return $this->belongsTo(HairLength::class);
    }
}
