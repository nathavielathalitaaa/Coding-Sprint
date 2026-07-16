<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel signer untuk surat turunan.
     * Setiap baris adalah satu slot penandatangan pada satu surat turunan.
     * Sekretaris memilih subset dari 3 jabatan slot saat generate.
     *
     * jabatan_slot dipakai untuk resolusi user aktual saat runtime,
     * mirip pola target_mode di surat_type_approvers:
     *   ketua_pelaksana  — Ketua Pelaksana kegiatan (dari organisasi terkait)
     *   pembina          — Pembina organisasi
     *   kepala_sekolah   — Kepala Sekolah (global/fixed)
     *
     * Alur TTD:
     *   waiting → (user TTD via PIN) → signed
     */
    public function up(): void
    {
        Schema::create('surat_turunan_signers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_turunan_id')
                  ->constrained('surat_turunans')
                  ->cascadeOnDelete()
                  ->comment('Surat turunan yang memerlukan TTD ini');

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User aktual yang harus TTD — diisi saat generate berdasarkan jabatan_slot');

            $table->string('jabatan_slot')
                  ->comment('Slot jabatan: ketua_pelaksana | pembina | kepala_sekolah');

            $table->string('status')
                  ->default('waiting')
                  ->comment('Status TTD: waiting | signed');

            $table->string('ttd_snapshot')
                  ->nullable()
                  ->comment('Path/base64 gambar TTD yang disematkan ke PDF, disimpan saat signed');

            $table->timestamp('signed_at')
                  ->nullable()
                  ->comment('Waktu TTD dilakukan');

            $table->timestamps();

            // Satu jabatan_slot hanya boleh muncul sekali per surat turunan
            $table->unique(['surat_turunan_id', 'jabatan_slot'], 'unique_turunan_slot');

            // Index untuk query "semua signer dari surat turunan X"
            $table->index(['surat_turunan_id', 'status'], 'idx_turunan_signer_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_turunan_signers');
    }
};
