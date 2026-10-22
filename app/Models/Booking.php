<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Booking extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'booking_number',
        'customer_id',
        'customer_address_id',
        'booking_type',
        'booking_date',
        'overall_start_time',
        'overall_end_time',
        'status',
        'subtotal_amount',
        'discount_amount',
        'vat_amount',
        'total_amount',
        'deposit_required',
        'deposit_paid',
        'payment_status',
        'admin_notes',
    ];

    public array $translatable = [
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'subtotal_amount' => 'float',
            'discount_amount' => 'float',
            'vat_amount' => 'float',
            'total_amount' => 'float',
            'deposit_required' => 'float',
            'deposit_paid' => 'float',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    public function customerAddress(): BelongsTo
    {
        return $this->address();
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
