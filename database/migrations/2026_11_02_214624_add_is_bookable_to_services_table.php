<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('services', 'is_bookable')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->boolean('is_bookable')->default(false)->after('is_popular');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('services', 'is_bookable')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->dropColumn('is_bookable');
            });
        }
    }
};
