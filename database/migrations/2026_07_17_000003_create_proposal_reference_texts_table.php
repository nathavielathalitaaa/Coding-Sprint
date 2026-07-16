<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_reference_texts', function (Blueprint $table) {
            $table->id();
            $table->string('section_key');
            $table->text('contoh_teks');
            $table->json('embedding_vector')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_reference_texts');
    }
};
