<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->foreignId('organisasi_id')->nullable()->after('surat_type_id')->constrained('organisasis')->nullOnDelete();
            $table->foreignId('komisi_id')->nullable()->after('organisasi_id')->constrained('komisis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['organisasi_id']);
            $table->dropForeign(['komisi_id']);
            $table->dropColumn(['organisasi_id', 'komisi_id']);
        });
    }
};
