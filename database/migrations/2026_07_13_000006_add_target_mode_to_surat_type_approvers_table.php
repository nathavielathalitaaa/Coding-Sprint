<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_type_approvers', function (Blueprint $table) {
            $table->string('target_mode')->default('submitter')->after('jabatan_label');
            // Enum: submitter | fixed_osis | fixed_mpk | global
        });
    }

    public function down(): void
    {
        Schema::table('surat_type_approvers', function (Blueprint $table) {
            $table->dropColumn('target_mode');
        });
    }
};
