<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialist_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialist_id')->constrained('specialists')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->unsignedSmallInteger('custom_duration_minutes')->nullable();
            $table->timestamps();

            $table->unique(['specialist_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_service');
    }
};
