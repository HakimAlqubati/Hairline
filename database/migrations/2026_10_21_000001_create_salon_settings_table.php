<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salon_settings', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('tagline')->nullable();
            $table->json('description_line1')->nullable();
            $table->json('description_line2')->nullable();
            $table->json('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('email')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('google_maps_url')->nullable();
            $table->string('tax_number')->nullable();
            $table->decimal('vat_percentage', 5, 2)->default(15.00);
            $table->json('stats')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_settings');
    }
};
