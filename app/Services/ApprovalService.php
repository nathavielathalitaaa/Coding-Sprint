<?php

namespace App\Services;

use App\Models\ApprovalStep;
use App\Models\DocumentApproval;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * ApprovalService
 * 
 * Manages multi-step document approval logic.
 */
class ApprovalService
{
    /**
     * Initialize all approval steps when a document is submitted.
     * Called once when a staff member submits a document.
     *
     * @param string $documentType Type of document (e.g., 'surat', 'purchase_requisition')
     * @param int $documentId ID of the document
     * @return bool True if steps are successfully created, false otherwise
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
                    'approver_id'      => null,
                    'status'           => $index === 0 ? 'waiting' : 'pending',
                ]);
            }
        });

        return true;
    }

    /**
     * Initialize approval steps from a SuratType definition.
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
                    'document_type'    => 'surat_' . $surat->suratType->kode,
                    'document_id'      => $surat->id,
                    'step_order'       => $approver->urutan,
                    'jabatan'          => $approver->jabatan_label,
                    'assigned_user_id' => $approver->user_id,
                    'label'            => $approver->label,
                    'metode_ttd'       => $approver->metode_ttd,
                    'approver_id'      => null,
                    'status'           => $index === 0 ? 'waiting' : 'pending',
                ]);
            }
        });

        return true;
    }

    /**
     * Approve the currently waiting step and activate the next step if available.
     *
     * @param string $documentType Type of the document
     * @param int $documentId ID of the document
     * @param User $approver User performing the approval
     * @param string $catatan Optional approval note
     * @param string|null $ttdSnapshot Optional path to the signature snapshot
     * @return array Array containing success status, message, and completion status
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
     * All subsequent steps remain pending.
     *
     * @param string $documentType Type of the document
     * @param int $documentId ID of the document
     * @param User $approver User performing the rejection
     * @param string $catatan Rejection reason (required)
     * @return array Array containing success status, message, and completion status
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
                'message' => "Dokumen ditolak oleh {$currentStep->label}. Kembali ke staff untuk revisi.",
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
     * 
     * @param string $documentType Type of the document
     * @param int $documentId ID of the document
     * @return bool True if successfully resubmitted
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
     * 
     * @param string $documentType Type of the document
     * @param int $documentId ID of the document
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStatus(string $documentType, int $documentId)
    {
        return DocumentApproval::forDocument($documentType, $documentId)->get();
    }

    /**
     * Check if the given user is allowed to approve the currently waiting step.
     * 
     * @param string $documentType Type of the document
     * @param int $documentId ID of the document
     * @param User $user User to check
     * @return bool True if allowed, false otherwise
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
     * 
     * @param DocumentApproval $step The step to action
     * @param User $user The user attempting the action
     * @return bool True if authorized
     */
    private function isUserAllowedForStep(DocumentApproval $step, User $user): bool
    {
        // ── Pengecekan User Spesifik ────────────────────────────────────
        if ($step->assigned_user_id) {
            return (int) $step->assigned_user_id === (int) $user->id;
        }

        $userRole = $user->role_name ?? '';
        
        if ($userRole === 'Pembina') {
            return true;
        }

        // ── Pengecekan Jabatan ──────────────────────────────────────────
        $jabatanUser = strtolower($user->profile?->jabatan ?? '');
        $jabatanStep = strtolower($step->jabatan ?? '');

        if ($jabatanUser === $jabatanStep) {
            return true;
        }

        if ($jabatanStep === 'bph' && in_array($userRole, ['BPH'])) {
            return true;
        }

        return false;
    }

    /**
     * Get the step currently waiting for approval.
     * 
     * @param string $documentType Type of the document
     * @param int $documentId ID of the document
     * @return DocumentApproval|null The waiting step or null
     */
    public function getWaitingStep(string $documentType, int $documentId): ?DocumentApproval
    {
        return DocumentApproval::forDocument($documentType, $documentId)
            ->where('status', 'waiting')
            ->first();
    }

    /**
     * Mark the currently waiting step as read by the authorized user.
     * 
     * @param string $documentType Type of the document
     * @param int $documentId ID of the document
     * @param User $user The user reading the document
     * @return void
     */
    public function markAsRead(string $documentType, int $documentId, User $user): void
    {
        $waitingStep = $this->getWaitingStep($documentType, $documentId);

        if ($waitingStep && $this->isUserAllowedForStep($waitingStep, $user)) {
            $waitingStep->update(['is_read' => true]);
        }
    }
}