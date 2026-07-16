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
        Schema::table('surat_types', function (Blueprint $table) {
            $table->foreignId('organisasi_id')->nullable()->after('organisasi_tipe')->constrained('organisasis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->dropForeign(['organisasi_id']);
            $table->dropColumn('organisasi_id');
        });
    }
};
