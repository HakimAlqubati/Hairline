<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('founder_stories', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('founder_name');
            $table->json('founder_title')->nullable();
            $table->json('intro_text')->nullable();
            $table->json('description_text')->nullable();
            $table->json('belief_text')->nullable();
            $table->json('founder_message_title')->nullable();
            $table->json('founder_message_paragraphs')->nullable();
            $table->unsignedSmallInteger('founding_year')->default(2020);
            $table->string('founder_image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('founder_stories');
    }
};
