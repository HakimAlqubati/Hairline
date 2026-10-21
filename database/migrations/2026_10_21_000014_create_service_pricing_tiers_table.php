<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('hair_length_id')->nullable()->constrained('hair_lengths')->cascadeOnDelete();
            $table->json('custom_label')->nullable();
            $table->decimal('price', 8, 2);
            $table->unsignedSmallInteger('extra_duration_minutes')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_pricing_tiers');
    }
};
