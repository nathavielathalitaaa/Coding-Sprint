<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom nomor_counter_reset_at ke tabel surat_types.
     *
     * Kolom ini menyimpan tanggal terakhir counter nomor surat direset.
     * Dipakai oleh SuratNumberService untuk menentukan kapan perlu reset counter
     * sesuai kebijakan nomor_reset ('yearly' | 'monthly' | 'never').
     *
     * nullable = surat type lama yang belum pernah reset tidak terpengaruh;
     * SuratNumberService akan mengisi kolom ini saat pertama kali reset dilakukan.
     */
    public function up(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->date('nomor_counter_reset_at')
                  ->nullable()
                  ->after('nomor_reset')
                  ->comment('Tanggal terakhir counter nomor surat direset; null = belum pernah reset');
        });
    }

    public function down(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->dropColumn('nomor_counter_reset_at');
        });
    }
};
