<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Cache\HairlineCacheService;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Bootstrap the model and its events.
     */
    protected static function booted(): void
    {
        static::saved(function (): void {
            HairlineCacheService::clearSalonInfo();
        });

        static::deleted(function (): void {
            HairlineCacheService::clearSalonInfo();
        });
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['key', 'value'];

    /**
     * Disables the auto-incrementing ID since we use 'key' as the primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Get a setting by its key.
     */
    public static function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by its key.
     */
    public static function setSetting(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value]
        );
    }

    /**
     * Convenient alias for getSetting.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::getSetting($key, $default);
    }
}
