<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom requires_kegiatan_detail ke tabel surat_types.
     * Jika true, surat jenis ini wajib mengisi detail kegiatan
     * (nama, tanggal, lokasi, dst) yang nantinya dipakai sebagai
     * sumber data auto-fill surat turunan.
     */
    public function up(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->boolean('requires_kegiatan_detail')
                  ->default(false)
                  ->after('organisasi_tipe')
                  ->comment('Jika true, surat ini memerlukan isian detail kegiatan untuk generate surat turunan');
        });
    }

    public function down(): void
    {
        Schema::table('surat_types', function (Blueprint $table) {
            $table->dropColumn('requires_kegiatan_detail');
        });
    }
};
