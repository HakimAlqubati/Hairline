<?php

declare(strict_types=1);

use App\Services\Cache\HairlineCacheService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_DEFAULT_HOURS = 'من 2:00 ظهراً حتى 10:00 مساءً';

    private const NEW_DEFAULT_HOURS = 'من 2:00 م حتى 10:00 م (عدا الأحد)';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $setting = DB::table('settings')->where('key', 'default_working_hours')->first();

        if (! $setting) {
            $now = now();
            DB::table('settings')->insert([
                'key' => 'default_working_hours',
                'value' => self::NEW_DEFAULT_HOURS,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } elseif ($setting->value === null || $setting->value === '' || $setting->value === self::OLD_DEFAULT_HOURS) {
            DB::table('settings')
                ->where('key', 'default_working_hours')
                ->update([
                    'value' => self::NEW_DEFAULT_HOURS,
                    'updated_at' => now(),
                ]);
        }

        HairlineCacheService::clearSalonInfo();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->where('key', 'default_working_hours')
            ->where('value', self::NEW_DEFAULT_HOURS)
            ->update([
                'value' => self::OLD_DEFAULT_HOURS,
                'updated_at' => now(),
            ]);

        HairlineCacheService::clearSalonInfo();
    }
};
