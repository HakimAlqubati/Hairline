<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->json('policy_text');
            $table->unsignedSmallInteger('free_cancellation_hours')->default(12);
            $table->unsignedSmallInteger('partial_refund_hours')->default(6);
            $table->unsignedTinyInteger('partial_refund_percentage')->default(50);
            $table->unsignedTinyInteger('no_show_penalty_percentage')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellation_policies');
    }
};
