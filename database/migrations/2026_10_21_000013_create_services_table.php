<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('service_groups')->nullOnDelete();
            $table->string('code')->unique();
            $table->json('name');
            $table->json('inclusions')->nullable();
            $table->json('description')->nullable();
            $table->json('price_display_override')->nullable();
            $table->decimal('base_price', 8, 2)->default(0.00);
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->unsignedSmallInteger('buffer_minutes')->default(10);
            $table->boolean('has_length_pricing')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_home_available')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
