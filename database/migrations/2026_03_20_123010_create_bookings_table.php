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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // 🔗 Target Identifiers
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();

            // 📅 Stay Window
            $table->date('start_date');
            $table->date('end_date');

            // 🔄 Stay Classification
            $table->enum('stay_type', ['short_term', 'long_term'])->default('long_term')->index();

            // 💰 Financial Audit Trail
            $table->decimal('price_per_period', 12, 2); // Captures the exact unit rate at the moment of booking
            $table->decimal('total_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0.00);

            // 🚥 Management Flags
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rejected'])->default('pending')->index();
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid', 'refunded'])->default('unpaid')->index();

            $table->text('tenant_notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            // ⚡ Scalability Indexes: Stops overlapping double-bookings at the hardware database layer
            $table->index(['unit_id', 'start_date', 'end_date', 'status'], 'idx_unit_availability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
