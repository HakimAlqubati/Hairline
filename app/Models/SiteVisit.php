<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SiteVisitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisit extends Model
{
    /** @use HasFactory<SiteVisitFactory> */
    use HasFactory, MassPrunable;

    public const DEVICE_MOBILE = 'mobile';

    public const DEVICE_TABLET = 'tablet';

    public const DEVICE_DESKTOP = 'desktop';

    public const DEVICE_UNKNOWN = 'unknown';

    public $timestamps = false;

    protected $fillable = [
        'device_type',
        'device_name',
        'os',
        'browser',
        'page_name',
        'path',
        'referer_host',
        'ip_address',
        'session_id',
        'customer_id',
        'user_agent',
        'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'customer_id' => 'integer',
            'visited_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the prunable model query for retaining 180 days of visit history.
     */
    public function prunable(): Builder
    {
        return static::where('visited_at', '<=', now()->subDays(180));
    }

    /**
     * @return array<string, string>
     */
    public static function deviceTypeOptions(): array
    {
        return [
            self::DEVICE_MOBILE => __('admin.site_visits.device_types.mobile'),
            self::DEVICE_TABLET => __('admin.site_visits.device_types.tablet'),
            self::DEVICE_DESKTOP => __('admin.site_visits.device_types.desktop'),
            self::DEVICE_UNKNOWN => __('admin.site_visits.device_types.unknown'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function pageNameOptions(): array
    {
        return [
            'home' => __('admin.site_visits.pages.home'),
            'services' => __('admin.site_visits.pages.services'),
            'specialists' => __('admin.site_visits.pages.specialists'),
            'booking' => __('admin.site_visits.pages.booking'),
            'about' => __('admin.site_visits.pages.about'),
            'profile' => __('admin.site_visits.pages.profile'),
            'my_bookings' => __('admin.site_visits.pages.my_bookings'),
            'bookings' => __('admin.site_visits.pages.bookings'),
            'contact' => __('admin.site_visits.pages.contact'),
            'privacy' => __('admin.site_visits.pages.privacy'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function osOptions(): array
    {
        return [
            'iOS' => 'iOS',
            'iPadOS' => 'iPadOS',
            'Android' => 'Android',
            'Windows' => 'Windows',
            'macOS' => 'macOS',
            'ChromeOS' => 'ChromeOS',
            'Linux' => 'Linux',
            'Unknown' => __('admin.site_visits.unknown'),
        ];
    }
}
