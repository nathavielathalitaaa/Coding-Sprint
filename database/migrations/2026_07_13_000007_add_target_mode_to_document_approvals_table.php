<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            // target_mode disalin dari SuratTypeApprover saat initFromSuratType()
            $table->string('target_mode')->default('submitter')->after('jabatan');
            // ID organisasi & komisi dari surat, disalin agar tidak perlu join ke surat setiap cek approval
            $table->unsignedBigInteger('surat_organisasi_id')->nullable()->after('target_mode');
            $table->unsignedBigInteger('surat_komisi_id')->nullable()->after('surat_organisasi_id');
        });
    }

    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn(['target_mode', 'surat_organisasi_id', 'surat_komisi_id']);
        });
    }
};
