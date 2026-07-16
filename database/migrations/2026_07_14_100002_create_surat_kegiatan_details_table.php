<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel detail kegiatan untuk surat induk.
     * Relasi 1-to-1 dengan surats (satu surat hanya punya satu detail kegiatan).
     * Data di sini menjadi sumber placeholder {{...}} untuk surat turunan.
     */
    public function up(): void
    {
        Schema::create('surat_kegiatan_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_id')
                  ->unique()
                  ->constrained('surats')
                  ->cascadeOnDelete()
                  ->comment('FK ke surats — unique karena 1 surat hanya punya 1 detail kegiatan');

            $table->string('nama_kegiatan')
                  ->comment('Nama kegiatan, e.g. "Pelantikan Pengurus OSIS 2026"');

            $table->date('tanggal_mulai')
                  ->comment('Tanggal mulai pelaksanaan kegiatan');

            $table->date('tanggal_selesai')
                  ->nullable()
                  ->comment('Tanggal selesai, nullable jika kegiatan sehari');

            $table->string('lokasi')
                  ->comment('Lokasi pelaksanaan kegiatan');

            $table->text('deskripsi_singkat')
                  ->nullable()
                  ->comment('Deskripsi singkat kegiatan untuk keperluan surat');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_kegiatan_details');
    }
};
