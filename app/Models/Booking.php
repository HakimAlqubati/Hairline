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

    public const CUSTOMER_TYPE_GUEST = 'guest';

    public const CUSTOMER_TYPE_REGISTERED = 'registered';

    // Booking Statuses
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_NO_SHOW = 'no_show';

    // Payment Statuses
    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    public const PAYMENT_STATUS_PARTIALLY_PAID = 'partially_paid';

    public const PAYMENT_STATUS_FULLY_PAID = 'fully_paid';

    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    // Booking Types
    public const BOOKING_TYPE_SALON = 'salon';

    public const BOOKING_TYPE_HOME = 'home';

    protected $fillable = [
        'booking_number',
        'customer_id',
        'customer_type',
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

    public function isGuest(): bool
    {
        return $this->customer_type === self::CUSTOMER_TYPE_GUEST;
    }

    public function isRegistered(): bool
    {
        return $this->customer_type === self::CUSTOMER_TYPE_REGISTERED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * @return array<int, string>
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_NO_SHOW,
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
