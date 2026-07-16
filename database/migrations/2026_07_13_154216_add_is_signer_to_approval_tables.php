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
        Schema::table('surat_type_approvers', function (Blueprint $table) {
            $table->boolean('is_signer')->default(true)->after('metode_ttd');
        });

        Schema::table('approval_steps', function (Blueprint $table) {
            $table->boolean('is_signer')->default(true)->after('ttd_mode');
        });

        Schema::table('document_approvals', function (Blueprint $table) {
            $table->boolean('is_signer')->default(true)->after('metode_ttd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_type_approvers', function (Blueprint $table) {
            $table->dropColumn('is_signer');
        });

        Schema::table('approval_steps', function (Blueprint $table) {
            $table->dropColumn('is_signer');
        });

        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn('is_signer');
        });
    }
};
