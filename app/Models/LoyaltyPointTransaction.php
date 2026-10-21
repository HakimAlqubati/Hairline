<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class LoyaltyPointTransaction extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'customer_id',
        'booking_id',
        'points_change',
        'balance_after',
        'description',
    ];

    public array $translatable = [
        'description',
    ];

    protected function casts(): array
    {
        return [
            'points_change' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
