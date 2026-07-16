<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel instance surat turunan.
     * Setiap baris adalah satu surat turunan yang digenerate dari surat induk.
     * Satu surat induk bisa memiliki banyak surat turunan (berbeda template).
     *
     * Alur status:
     *   draft → (sekretaris submit ke signer) → menunggu_ttd → (semua signer TTD) → ditandatangani
     */
    public function up(): void
    {
        Schema::create('surat_turunans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_id')
                  ->constrained('surats')
                  ->cascadeOnDelete()
                  ->comment('Surat induk yang sudah approved_owner');

            $table->foreignId('surat_turunan_template_id')
                  ->constrained('surat_turunan_templates')
                  ->comment('Template yang digunakan saat generate');

            $table->string('nomor_surat')
                  ->nullable()
                  ->comment('Nomor surat turunan, diisi saat generate (format: KODE/ID/TAHUN)');

            $table->longText('konten_final')
                  ->comment('Hasil render template setelah placeholder diganti dengan data asli');

            $table->string('file_pdf_path')
                  ->nullable()
                  ->comment('Path PDF final, diisi setelah SEMUA signer sudah TTD');

            $table->string('status')
                  ->default('draft')
                  ->comment('Status: draft | menunggu_ttd | ditandatangani');

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->comment('Sekretaris yang meng-generate surat turunan ini');

            $table->timestamps();

            // Composite index untuk query "surat turunan milik surat induk X"
            $table->index(['surat_id', 'status'], 'idx_surat_turunan_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_turunans');
    }
};
