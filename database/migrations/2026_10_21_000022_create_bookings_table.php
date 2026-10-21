<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('customer_address_id')->nullable()->constrained('customer_addresses')->nullOnDelete();
            $table->string('booking_type')->default('salon'); // 'salon', 'home'
            $table->date('booking_date');
            $table->time('overall_start_time');
            $table->time('overall_end_time');
            $table->string('status')->default('confirmed'); // 'pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'
            $table->decimal('subtotal_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('vat_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('deposit_required', 10, 2)->default(0.00);
            $table->decimal('deposit_paid', 10, 2)->default(0.00);
            $table->string('payment_status')->default('unpaid'); // 'unpaid', 'partially_paid', 'fully_paid', 'refunded'
            $table->json('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['booking_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
