<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_format_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->unique()->constrained('surats')->onDelete('cascade');
            $table->integer('skor_struktur');
            $table->integer('skor_konten')->nullable();
            $table->integer('skor_akhir');
            $table->json('detail');
            $table->timestamp('checked_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_format_checks');
    }
};
