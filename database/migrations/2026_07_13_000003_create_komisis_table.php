<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komisis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            // FK ke organisasis — sebaiknya hanya organisasi bertipe 'mpk'
            $table->foreignId('organisasi_id')->constrained('organisasis')->cascadeOnDelete();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komisis');
    }
};
