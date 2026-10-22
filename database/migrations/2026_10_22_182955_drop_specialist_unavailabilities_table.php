<?php

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
        Schema::dropIfExists('specialist_unavailabilities');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('specialist_unavailabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialist_id')->constrained('specialists')->cascadeOnDelete();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->json('reason')->nullable();
            $table->timestamps();

            $table->index(['specialist_id', 'start_datetime', 'end_datetime'], 'spec_unavail_idx');
        });
    }
};
