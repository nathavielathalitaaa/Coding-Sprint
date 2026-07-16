<?php

namespace App\Services;

use App\Models\ApprovalStep;
use App\Models\DocumentApproval;
use App\Models\Organisasi;
use App\Models\OrganisasiMember;
use App\Models\KomisiMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * ApprovalService
 *
 * Manages multi-step document approval logic for SIMORA.
 * Approval authorization is based on target_mode (not profile->jabatan).
 *
 * target_mode values:
 *   submitter  — user harus OrganisasiMember dari organisasi pengaju surat, jabatan sesuai step
 *   fixed_osis — user harus OrganisasiMember dari Organisasi tipe='osis', jabatan sesuai step
 *   fixed_mpk  — user harus OrganisasiMember dari Organisasi tipe='mpk', jabatan sesuai step
 *   global     — user harus punya Spatie role 'pengawas_pusat' atau 'kepala_sekolah'
 */
class ApprovalService
{
    /**
     * Initialize all approval steps when a document is submitted (legacy method).
     */
    public function initApproval(string $documentType, int $documentId): bool
    {
        $steps = ApprovalStep::stepsFor($documentType);

        if ($steps->isEmpty()) {
            return false;
        }

        DB::transaction(function () use ($steps, $documentType, $documentId) {
            foreach ($steps as $index => $step) {
                DocumentApproval::create([
                    'document_type'    => $documentType,
                    'document_id'      => $documentId,
                    'step_order'       => $step->step_order,
                    'jabatan'          => $step->jabatan,
                    'assigned_user_id' => $step->user_id,
                    'label'            => $step->label,
                    'is_signer'        => $step->is_signer ?? true,
                    'approver_id'      => null,
                    'status'           => $index === 0 ? 'waiting' : 'pending',
                ]);
            }
        });

        return true;
    }

    /**
     * Initialize approval steps from a SuratType definition.
     * Copies target_mode, surat_organisasi_id, surat_komisi_id to each DocumentApproval
     * so that isUserAllowedForStep() can work without joining back to Surat.
     *
     * @param \App\Models\Surat $surat The surat instance
     * @return bool True if steps are successfully created, false otherwise
     */
    public function initFromSuratType(\App\Models\Surat $surat): bool
    {
        $suratType = $surat->suratType;

        if (!$suratType) {
            return false;
        }

        $approvers = $suratType->approvers;

        if ($approvers->isEmpty()) {
            return false;
        }

        DB::transaction(function () use ($approvers, $surat) {
            foreach ($approvers as $index => $approver) {
                DocumentApproval::create([
                    'document_type'      => 'surat_' . $surat->suratType->kode,
                    'document_id'        => $surat->id,
                    'step_order'         => $approver->urutan,
                    'jabatan'            => $approver->jabatan_label,
                    'target_mode'        => $approver->target_mode ?? 'submitter',
                    'surat_organisasi_id'=> $surat->organisasi_id,
                    'surat_komisi_id'    => $surat->komisi_id,
                    'assigned_user_id'   => $approver->user_id,
                    'label'              => $approver->label,
                    'metode_ttd'         => $approver->metode_ttd,
                    'is_signer'          => $approver->is_signer ?? true,
                    'approver_id'        => null,
                    'status'             => $index === 0 ? 'waiting' : 'pending',
                ]);
            }
        });

        return true;
    }

    /**
     * Approve the currently waiting step and activate the next step if available.
     */
    public function approve(string $documentType, int $documentId, User $approver, string $catatan = '', ?string $ttdSnapshot = null): array
    {
        // ── Validasi Step Aktif ─────────────────────────────────────────
        $currentStep = DocumentApproval::forDocument($documentType, $documentId)
            ->where('status', 'waiting')
            ->first();

        if (!$currentStep) {
            return [
                'success' => false,
                'message' => 'Tidak ada step yang menunggu approval.',
                'selesai' => false
            ];
        }

        // ── Validasi Otorisasi Approver ─────────────────────────────────
        if (!$this->isUserAllowedForStep($currentStep, $approver)) {
            return [
                'success' => false,
                'message' => "Bukan giliran Anda untuk approve. Step ini untuk {$currentStep->label} (jabatan: {$currentStep->jabatan}).",
                'selesai' => false,
            ];
        }

        // ── Proses Approval & Update Step ───────────────────────────────
        try {
            DB::transaction(function () use ($currentStep, $approver, $catatan, $documentType, $documentId, $ttdSnapshot) {
                $currentStep->update([
                    'status'       => 'approved',
                    'approver_id'  => $approver->id,
                    'catatan'      => $catatan,
                    'actioned_at'  => now(),
                    'ttd_snapshot' => $ttdSnapshot,
                ]);

                $nextStep = DocumentApproval::forDocument($documentType, $documentId)
                    ->where('step_order', $currentStep->step_order + 1)
                    ->where('status', 'pending')
                    ->first();

                if ($nextStep) {
                    $nextStep->update(['status' => 'waiting']);
                }
            });

            // ── Pengecekan Status Selesai ───────────────────────────────
            $sisaPending = DocumentApproval::forDocument($documentType, $documentId)
                ->whereIn('status', ['pending', 'waiting'])
                ->count();

            $selesai = $sisaPending === 0;

            if ($selesai && str_starts_with($documentType, 'surat_')) {
                $surat = \App\Models\Surat::find($documentId);
                if ($surat && $surat->suratType && $surat->suratType->requires_kegiatan_detail) {
                    $surat->update([
                        'pic_user_id' => $surat->user_id,
                        'status_pelaksanaan' => 'belum_mulai',
                    ]);

                    $kegiatanNama = $surat->kegiatanDetail->nama_kegiatan ?? $surat->perihal;
                    try {
                        $notif = app(\App\Services\NotificationService::class);
                        $notif->send(
                            $surat->user_id,
                            'Penugasan PIC Kegiatan',
                            "Anda ditugaskan sebagai PIC untuk pelaksanaan kegiatan: {$kegiatanNama}.",
                            route('pelaksanaan.index')
                        );
                    } catch (\Exception $ex) {
                        \Log::error("Failed to send PIC assignment notification: " . $ex->getMessage());
                    }
                }
            }

            return [
                'success' => true,
                'message' => $selesai
                    ? 'Semua approval selesai. Dokumen telah disetujui penuh.'
                    : "Step {$currentStep->label} disetujui. Menunggu approval berikutnya.",
                'selesai' => $selesai,
            ];
        } catch (\Exception $e) {
            \Log::error("Approval error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses approval.',
                'selesai' => false,
            ];
        }
    }

    /**
     * Reject the active step, returning the document to staff for revision.
     */
    public function reject(string $documentType, int $documentId, User $approver, string $catatan): array
    {
        // ── Validasi Step Aktif ─────────────────────────────────────────
        $currentStep = DocumentApproval::forDocument($documentType, $documentId)
            ->where('status', 'waiting')
            ->first();

        if (!$currentStep) {
            return [
                'success' => false,
                'message' => 'Tidak ada step yang aktif.',
                'selesai' => false
            ];
        }

        // ── Validasi Otorisasi Approver ─────────────────────────────────
        if (!$this->isUserAllowedForStep($currentStep, $approver)) {
            return [
                'success' => false,
                'message' => "Bukan giliran Anda untuk menolak. Step ini untuk {$currentStep->label} (jabatan: {$currentStep->jabatan}).",
                'selesai' => false,
            ];
        }

        // ── Proses Rejection ────────────────────────────────────────────
        try {
            DB::transaction(function () use ($currentStep, $approver, $catatan) {
                $currentStep->update([
                    'status'      => 'rejected',
                    'approver_id' => $approver->id,
                    'catatan'     => $catatan,
                    'actioned_at' => now(),
                ]);
            });

            return [
                'success' => true,
                'message' => "Dokumen ditolak oleh {$currentStep->label}. Kembali ke pengaju untuk revisi.",
                'selesai' => false,
            ];
        } catch (\Exception $e) {
            \Log::error("Rejection error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses penolakan.',
                'selesai' => false,
            ];
        }
    }

    /**
     * Resubmit a document after revision.
     * Deletes old approval logs and re-initializes from step 1.
     * Logic tidak diubah sesuai spesifikasi.
     */
    public function resubmit(string $documentType, int $documentId): bool
    {
        try {
            DB::transaction(function () use ($documentType, $documentId) {
                DocumentApproval::forDocument($documentType, $documentId)->delete();

                if (str_starts_with($documentType, 'surat_')) {
                    $surat = \App\Models\Surat::find($documentId);

                    if ($surat) {
                        $this->initFromSuratType($surat);
                    }
                } else {
                    $this->initApproval($documentType, $documentId);
                }
            });

            return true;
        } catch (\Exception $e) {
            \Log::error("Resubmit error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the complete approval status sequentially for a document.
     */
    public function getStatus(string $documentType, int $documentId)
    {
        return DocumentApproval::forDocument($documentType, $documentId)->get();
    }

    /**
     * Check if the given user is allowed to approve the currently waiting step.
     */
    public function canApprove(string $documentType, int $documentId, User $user): bool
    {
        $waitingStep = DocumentApproval::forDocument($documentType, $documentId)
            ->where('status', 'waiting')
            ->first();

        if (!$waitingStep) {
            return false;
        }

        return $this->isUserAllowedForStep($waitingStep, $user);
    }

    /**
     * Check if a specific user has authorization to action a specific step.
     * Logika baru berbasis target_mode (tidak lagi memakai profile->jabatan).
     *
     * @param DocumentApproval $step The step to action
     * @param User $user The user attempting the action
     * @return bool True if authorized
     */
    public function isUserAllowedForStep(DocumentApproval $step, User $user): bool
    {
        // ── Super-admin bypass semua pengecekan ─────────────────────────
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // ── Pengecekan User Spesifik (assigned_user_id) ─────────────────
        if ($step->assigned_user_id) {
            return (int) $step->assigned_user_id === (int) $user->id;
        }

        $jabatanStep = strtolower($step->jabatan ?? '');
        $targetMode  = $step->target_mode ?? 'submitter';

        switch ($targetMode) {
            case 'submitter':
                // User harus OrganisasiMember dari organisasi pengaju surat
                return $this->checkSubmitterMember($user, $step->surat_organisasi_id, $jabatanStep, $step->surat_komisi_id);

            case 'fixed_osis':
                // User harus OrganisasiMember dari Organisasi tipe='osis'
                return $this->checkFixedOrganisasi($user, 'osis', $jabatanStep);

            case 'fixed_mpk':
                // User harus OrganisasiMember dari Organisasi tipe='mpk'
                return $this->checkFixedOrganisasi($user, 'mpk', $jabatanStep);

            case 'global':
                // User harus punya role Spatie sesuai jabatan step
                return $this->checkGlobalRole($user, $jabatanStep);

            default:
                return false;
        }
    }

    /**
     * Cek apakah user adalah anggota dari organisasi pengaju surat dengan jabatan yang sesuai.
     * Jika jabatan='komisi', juga cek KomisiMember untuk komisi_id yang sesuai.
     */
    private function checkSubmitterMember(User $user, ?int $organisasiId, string $jabatan, ?int $komisiId): bool
    {
        if (!$organisasiId) {
            return false;
        }

        // Jika jabatan komisi, cek KomisiMember dulu
        if ($jabatan === 'komisi') {
            if (!$komisiId) {
                return false;
            }
            return KomisiMember::where('user_id', $user->id)
                ->where('komisi_id', $komisiId)
                ->exists();
        }

        // Cek OrganisasiMember
        return OrganisasiMember::where('user_id', $user->id)
            ->where('organisasi_id', $organisasiId)
            ->where('jabatan', $jabatan)
            ->exists();
    }

    /**
     * Cek apakah user adalah anggota dari Organisasi dengan tipe tertentu (osis/mpk).
     */
    private function checkFixedOrganisasi(User $user, string $tipe, string $jabatan): bool
    {
        $organisasi = Organisasi::where('tipe', $tipe)
            ->where('is_active', true)
            ->first();

        if (!$organisasi) {
            return false;
        }

        return OrganisasiMember::where('user_id', $user->id)
            ->where('organisasi_id', $organisasi->id)
            ->where('jabatan', $jabatan)
            ->exists();
    }

    /**
     * Cek apakah user punya role global Spatie sesuai jabatan step.
     * Jabatan 'pengawas_pusat' → role 'pengawas_pusat'
     * Jabatan 'kepala_sekolah' → role 'kepala_sekolah'
     */
    private function checkGlobalRole(User $user, string $jabatan): bool
    {
        // Normalisasi jabatan ke nama role Spatie
        $roleMap = [
            'pengawas_pusat' => 'pengawas_pusat',
            'kepala_sekolah' => 'kepala_sekolah',
        ];

        $role = $roleMap[$jabatan] ?? $jabatan;

        return $user->hasRole($role);
    }

    /**
     * Get the step currently waiting for approval.
     */
    public function getWaitingStep(string $documentType, int $documentId): ?DocumentApproval
    {
        return DocumentApproval::forDocument($documentType, $documentId)
            ->where('status', 'waiting')
            ->first();
    }

    /**
     * Mark the currently waiting step as read by the authorized user.
     */
    public function markAsRead(string $documentType, int $documentId, User $user): void
    {
        $waitingStep = $this->getWaitingStep($documentType, $documentId);

        if ($waitingStep && $this->isUserAllowedForStep($waitingStep, $user)) {
            $waitingStep->update(['is_read' => true]);
        }
    }

    /**
     * Check if the currently waiting step requires a digital signature (is_signer = true).
     *
     * @param string $documentType The document type identifier
     * @param int $documentId The document ID
     * @return bool True if the current step requires a signature, false otherwise
     */
    public function currentStepRequiresSignature(string $documentType, int $documentId): bool
    {
        $waitingStep = $this->getWaitingStep($documentType, $documentId);

        if (!$waitingStep) {
            return false;
        }

        return (bool) $waitingStep->is_signer;
    }
}
