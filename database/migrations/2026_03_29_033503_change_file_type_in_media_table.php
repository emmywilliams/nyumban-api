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
        Schema::table('media', function (Blueprint $table) {
            // This converts the ENUM to a standard string (VARCHAR 255)
            $table->string('file_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // This would roll it back to the ENUM if needed
            $table->enum('file_type', ['photo', 'document', 'video'])->change();
        });
    }
};
