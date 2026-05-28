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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();

            // Location
            $table->foreignId('district_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('county_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_county_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parish_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();

            // Property details
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('address')->nullable();

            $table->boolean('is_gated')->default(false);
            $table->boolean('is_multi_unit')->default(false);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['active', 'inactive', 'under_verification'])->default('inactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
