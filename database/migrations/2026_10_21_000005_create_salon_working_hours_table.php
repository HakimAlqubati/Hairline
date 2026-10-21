<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salon_working_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week'); // 0 = Sunday, 6 = Saturday
            $table->json('day_label');
            $table->time('opening_time')->default('14:00:00');
            $table->time('closing_time')->default('22:00:00');
            $table->boolean('is_closed')->default(false);
            $table->json('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_working_hours');
    }
};
