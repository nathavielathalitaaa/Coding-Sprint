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
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('nomor_surat')->unique();
            $table->string('jenis_surat');
            $table->string('perihal');
            $table->string('file_pdf');
            $table->enum('status', ['submitted', 'approved_supervisor', 'approved_owner', 'rejected', 'revised'])->default('submitted');
            $table->foreignId('approved_by_supervisor')->nullable()->constrained('users');
            $table->foreignId('approved_by_owner')->nullable()->constrained('users');
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
