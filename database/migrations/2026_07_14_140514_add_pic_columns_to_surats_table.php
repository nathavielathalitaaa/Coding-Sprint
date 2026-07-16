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
        Schema::table('surats', function (Blueprint $table) {
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status_pelaksanaan', ['belum_mulai', 'berjalan', 'selesai'])->default('belum_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['pic_user_id']);
            $table->dropColumn(['pic_user_id', 'status_pelaksanaan']);
        });
    }
};
