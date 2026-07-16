<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update surat_type_approvers configurations
        // OSIS flow: Step 2 (Disetujui BPH MPK / target_mode = fixed_mpk)
        $osisType = DB::table('surat_types')->where('kode', 'osis')->first();
        if ($osisType) {
            DB::table('surat_type_approvers')
                ->where('surat_type_id', $osisType->id)
                ->where('urutan', 2)
                ->where('target_mode', 'fixed_mpk')
                ->update(['is_signer' => false]);
        }

        // Sub Organ flow: Step 2 (Disetujui BPH OSIS / target_mode = fixed_osis)
        // Sub Organ flow: Step 3 (Disetujui BPH MPK / target_mode = fixed_mpk)
        $subOrganType = DB::table('surat_types')->where('kode', 'sub_organ')->first();
        if ($subOrganType) {
            DB::table('surat_type_approvers')
                ->where('surat_type_id', $subOrganType->id)
                ->where('urutan', 2)
                ->where('target_mode', 'fixed_osis')
                ->update(['is_signer' => false]);

            DB::table('surat_type_approvers')
                ->where('surat_type_id', $subOrganType->id)
                ->where('urutan', 3)
                ->where('target_mode', 'fixed_mpk')
                ->update(['is_signer' => false]);
        }

        // 2. Update existing document_approvals
        // OSIS document: Step 2 (Disetujui BPH MPK / target_mode = fixed_mpk)
        DB::table('document_approvals')
            ->where('document_type', 'surat_osis')
            ->where('step_order', 2)
            ->where('target_mode', 'fixed_mpk')
            ->update(['is_signer' => false]);

        // Sub Organ document: Step 2 and 3
        DB::table('document_approvals')
            ->where('document_type', 'surat_sub_organ')
            ->where('step_order', 2)
            ->where('target_mode', 'fixed_osis')
            ->update(['is_signer' => false]);

        DB::table('document_approvals')
            ->where('document_type', 'surat_sub_organ')
            ->where('step_order', 3)
            ->where('target_mode', 'fixed_mpk')
            ->update(['is_signer' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $osisType = DB::table('surat_types')->where('kode', 'osis')->first();
        if ($osisType) {
            DB::table('surat_type_approvers')
                ->where('surat_type_id', $osisType->id)
                ->where('urutan', 2)
                ->where('target_mode', 'fixed_mpk')
                ->update(['is_signer' => true]);
        }

        $subOrganType = DB::table('surat_types')->where('kode', 'sub_organ')->first();
        if ($subOrganType) {
            DB::table('surat_type_approvers')
                ->where('surat_type_id', $subOrganType->id)
                ->where('urutan', 2)
                ->where('target_mode', 'fixed_osis')
                ->update(['is_signer' => true]);

            DB::table('surat_type_approvers')
                ->where('surat_type_id', $subOrganType->id)
                ->where('urutan', 3)
                ->where('target_mode', 'fixed_mpk')
                ->update(['is_signer' => true]);
        }

        DB::table('document_approvals')
            ->where('document_type', 'surat_osis')
            ->where('step_order', 2)
            ->where('target_mode', 'fixed_mpk')
            ->update(['is_signer' => true]);

        DB::table('document_approvals')
            ->where('document_type', 'surat_sub_organ')
            ->where('step_order', 2)
            ->where('target_mode', 'fixed_osis')
            ->update(['is_signer' => true]);

        DB::table('document_approvals')
            ->where('document_type', 'surat_sub_organ')
            ->where('step_order', 3)
            ->where('target_mode', 'fixed_mpk')
            ->update(['is_signer' => true]);
    }
};
