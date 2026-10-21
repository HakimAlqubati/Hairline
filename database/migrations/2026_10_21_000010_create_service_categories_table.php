<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_page_id')->nullable()->constrained('catalog_pages')->nullOnDelete();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('subtitle')->nullable();
            $table->string('icon')->default('scissors');
            $table->unsignedTinyInteger('page_ref')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
