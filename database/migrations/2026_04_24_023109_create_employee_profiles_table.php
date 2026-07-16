<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // ── Jabatan Struktural (label bebas, BUKAN untuk routing approval) ──
            // Approval routing 100% lewat OrganisasiMember->jabatan
            $table->string('jabatan_struktural')->nullable()->comment('Contoh: Kelas XI RPL 2, atau posisi non-approval lainnya');

            // ── Kontak ────────────────────────────────────
            $table->string('no_telepon', 20)->nullable();
            $table->date('tgl_bergabung')->nullable()->comment('Tanggal bergabung sebagai pengurus');

            // ── Lokasi ────────────────────────────────────
            $table->text('alamat')->nullable();

            // ── TTD & PIN untuk sistem approval ──────────
            $table->string('ttd_path')->nullable()->comment('Path file tanda tangan (onboarding manual)');
            $table->string('signature_path')->nullable()->comment('Path file tanda tangan digital');
            $table->string('pin', 255)->nullable()->comment('PIN approval (hashed bcrypt)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
