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
        Schema::dropIfExists('jadwal_shifts');
        Schema::dropIfExists('shifts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for drop
    }
};
