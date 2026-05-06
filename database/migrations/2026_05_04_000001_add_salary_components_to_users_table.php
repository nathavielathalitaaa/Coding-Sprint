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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('tunjangan_jabatan', 15, 2)->default(0)->after('gaji_pokok');
            $table->decimal('tunjangan_makan', 15, 2)->default(0)->after('tunjangan_jabatan');
            $table->decimal('tunjangan_transport', 15, 2)->default(0)->after('tunjangan_makan');
            $table->decimal('potongan_bpjs', 15, 2)->default(0)->after('tunjangan_transport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tunjangan_jabatan',
                'tunjangan_makan',
                'tunjangan_transport',
                'potongan_bpjs'
            ]);
        });
    }
};
