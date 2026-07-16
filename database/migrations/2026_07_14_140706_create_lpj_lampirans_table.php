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
        Schema::create('lpj_lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpj_id')->constrained('laporan_pertanggungjawabans')->cascadeOnDelete();
            $table->string('file_path');
            $table->enum('tipe', ['foto', 'video', 'kwitansi', 'lainnya']);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpj_lampirans');
    }
};
