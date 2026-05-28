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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Studio', 'Free WiFi'
            $table->string('slug')->unique();
            $table->string('type')->default('amenity'); // 'unit_type' or 'amenity'
            $table->string('icon')->nullable(); // For Flutter icons (e.g., 'wifi', 'bed')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
