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
        Schema::create('document_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default values
        $now = now();
        \Illuminate\Support\Facades\DB::table('document_settings')->insert([
            ['key' => 'company_name', 'value' => 'SIMORA SMK Telkom Sidoarjo', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'accent_color', 'value' => '#E62129', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'font_family', 'value' => 'Arial', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_text', 'value' => 'Dokumen ini digenerate otomatis oleh sistem persuratan SIMORA', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'logo_path', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_settings');
    }
};
