<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CancellationPolicy extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'policy_text',
        'free_cancellation_hours',
        'partial_refund_hours',
        'partial_refund_percentage',
        'no_show_penalty_percentage',
        'is_active',
    ];

    public array $translatable = [
        'policy_text',
    ];

    protected function casts(): array
    {
        return [
            'free_cancellation_hours' => 'integer',
            'partial_refund_hours' => 'integer',
            'partial_refund_percentage' => 'integer',
            'no_show_penalty_percentage' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
