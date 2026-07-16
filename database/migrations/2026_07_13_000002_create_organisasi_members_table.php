<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisasi_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organisasi_id')->constrained('organisasis')->cascadeOnDelete();
            $table->enum('jabatan', ['anggota', 'sekretaris', 'ketua', 'bph', 'komisi', 'pembina', 'pengawas']);
            $table->timestamps();

            // Satu user hanya bisa punya satu jabatan per organisasi
            $table->unique(['user_id', 'organisasi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisasi_members');
    }
};
