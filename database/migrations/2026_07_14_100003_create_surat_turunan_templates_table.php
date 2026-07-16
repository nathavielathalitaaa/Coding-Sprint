<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel template surat turunan.
     * Setiap baris adalah jenis surat turunan yang bisa digenerate
     * dari surat induk yang sudah approved_owner.
     *
     * Placeholder yang didukung dalam konten_template:
     *   {{nama_kegiatan}}       — dari surat_kegiatan_details.nama_kegiatan
     *   {{tanggal_mulai}}       — dari surat_kegiatan_details.tanggal_mulai
     *   {{tanggal_selesai}}     — dari surat_kegiatan_details.tanggal_selesai
     *   {{lokasi}}              — dari surat_kegiatan_details.lokasi
     *   {{deskripsi_singkat}}   — dari surat_kegiatan_details.deskripsi_singkat
     *   {{organisasi_nama}}     — dari organisasi yang terkait surat induk
     *   {{nomor_surat_induk}}   — dari surats.nomor_surat
     *   {{tanggal_surat}}       — tanggal generate (today)
     */
    public function up(): void
    {
        Schema::create('surat_turunan_templates', function (Blueprint $table) {
            $table->id();

            $table->string('kode')
                  ->unique()
                  ->comment('Slug unik: undangan | izin_kegiatan | peminjaman_tempat | sponsorship');

            $table->string('nama')
                  ->comment('Nama tampilan, e.g. "Surat Undangan"');

            $table->longText('konten_template')
                  ->comment('Isi teks baku dengan placeholder {{...}} yang akan dirender saat generate');

            $table->boolean('is_active')
                  ->default(true)
                  ->comment('Nonaktifkan tanpa hapus data');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_turunan_templates');
    }
};
