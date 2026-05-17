<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surats';

    protected $fillable = [
        'user_id',
        'surat_type_id',
        'nomor_surat',
        'jenis_surat',
        'perihal',
        'file_pdf',
        'cover_pdf_path',
        'final_pdf_path',
        'status',
        'catatan_revisi',
        'ttd_coordinates',
    ];

    protected $casts = [
        'ttd_coordinates' => 'array',
    ];

    // ── helper: cek apakah punya final_pdf ─────────────
    public function hasFinalPdf(): bool
    {
        return !empty($this->final_pdf_path);
    }

    // ── relasi ke user (pembuat) ───────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── relasi ke jenis surat ─────────────────────────
    public function suratType()
    {
        return $this->belongsTo(SuratType::class);
    }

    // ── relasi ke documentapproval (log 4 step) ────────
    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class, 'document_id')
            ->where('document_type', 'LIKE', 'surat_%')
            ->orderBy('step_order');
    }

    // ── helper: ambil step yang sedang waiting ─────────
    public function waitingStep(): ?DocumentApproval
    {
        return $this->approvals()->where('status', 'waiting')->first();
    }

    // ── helper: cek apakah semua step sudah approved ───
    public function isFullyApproved(): bool
    {
        return $this->approvals()->whereNotIn('status', ['approved'])->doesntExist();
    }

    // ── helper: cek apakah bisa diedit (oleh pembuat) ──
    public function canBeEdited(): bool
    {
        // cuma bs diedit klo status 'revised' (abis ditolak/perlu revisi)
        // status 'submitted' gk bs diedit lagi
        return $this->status === 'revised';
    }

    // ── helper: cek apakah bisa dihapus (oleh pembuat) ──
    public function canBeDeleted(): bool
    {
        // cuma bs dihapus klo status submitted & blm ada approval diproses
        if ($this->status !== 'submitted') {
            return false;
        }

        $hasProcessedApproval = $this->approvals()
            ->whereIn('status', ['approved', 'rejected'])
            ->exists();

        return !$hasProcessedApproval;
    }

    // ── label status untuk tampilan ────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted'      => 'Diajukan',
            'approved_owner' => 'Disetujui Penuh',
            'revised'        => 'Perlu Revisi',
            'rejected'       => 'Ditolak',
            default          => ucfirst($this->status),
        };
    }

    // ── warna badge per status ─────────────────────────
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'submitted'      => 'b-blue',
            'approved_owner' => 'b-green',
            'revised'        => 'b-amber',
            'rejected'       => 'b-red',
            default          => 'b-gray',
        };
    }
}
