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
        Schema::create('laporan_pertanggungjawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->unique()->constrained('surats')->cascadeOnDelete();
            $table->text('ringkasan_kegiatan');
            $table->json('realisasi_anggaran')->nullable();
            $table->enum('status', ['draft', 'submitted', 'revisi', 'valid'])->default('draft');
            $table->text('catatan_revisi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('ttd_path')->nullable();
            $table->text('keywords')->nullable();
            $table->json('embedding_vector')->nullable();
            $table->timestamp('embedded_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE laporan_pertanggungjawabans ADD FULLTEXT INDEX lpj_keywords_fulltext (keywords)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pertanggungjawabans');
    }
};
