<?php

declare(strict_types=1);

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
        Schema::create('site_visits', function (Blueprint $table): void {
            $table->id();
            $table->string('device_type', 20)->default('unknown');
            $table->string('device_name', 60)->nullable();
            $table->string('os', 40)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('page_name', 50)->nullable();
            $table->string('path', 255);
            $table->string('referer_host', 120)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('visited_at')->useCurrent();

            $table->index('visited_at');
            $table->index(['device_type', 'visited_at']);
            $table->index('os');
            $table->index('page_name');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
