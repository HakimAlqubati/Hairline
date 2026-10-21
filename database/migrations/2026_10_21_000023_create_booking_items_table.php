<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('specialist_id')->constrained('specialists')->cascadeOnDelete();
            $table->foreignId('hair_length_id')->nullable()->constrained('hair_lengths')->nullOnDelete();
            $table->decimal('unit_price', 8, 2);
            $table->unsignedSmallInteger('duration_minutes');
            $table->time('scheduled_start_time');
            $table->time('scheduled_end_time');
            $table->string('status')->default('confirmed');
            $table->timestamps();

            $table->index(['specialist_id', 'scheduled_start_time', 'scheduled_end_time'], 'spec_sched_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
