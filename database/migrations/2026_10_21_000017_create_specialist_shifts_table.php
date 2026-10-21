<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialist_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialist_id')->constrained('specialists')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0 = Sunday, 6 = Saturday
            $table->time('start_time')->default('14:00:00');
            $table->time('end_time')->default('22:00:00');
            $table->boolean('is_day_off')->default(false);
            $table->timestamps();

            $table->unique(['specialist_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_shifts');
    }
};
