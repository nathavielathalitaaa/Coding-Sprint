<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komisi_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('komisi_id')->constrained('komisis')->cascadeOnDelete();
            $table->timestamps();

            // Satu user hanya bisa masuk satu komisi yang sama sekali
            $table->unique(['user_id', 'komisi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komisi_members');
    }
};
