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
        'organisasi_id',
        'komisi_id',
        'nomor_surat',
        'jenis_surat',
        'perihal',
        'file_pdf',
        'cover_pdf_path',
        'final_pdf_path',
        'status',
        'catatan_revisi',
        'ttd_coordinates',
        'pic_user_id',
        'status_pelaksanaan',
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

    // ── relasi ke organisasi pengaju ──────────────────
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class);
    }

    // ── relasi ke komisi (khusus flow MPK) ───────────
    public function komisi()
    {
        return $this->belongsTo(Komisi::class);
    }

    // ── relasi ke detail kegiatan (1-to-1) ───────────
    public function kegiatanDetail()
    {
        return $this->hasOne(SuratKegiatanDetail::class);
    }

    // ── relasi ke surat turunan (1-to-many) ───────────
    public function suratTurunans()
    {
        return $this->hasMany(SuratTurunan::class);
    }

    // ── relasi PIC, Progress, dan LPJ ────────────────
    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'surat_id');
    }

    public function lpj()
    {
        return $this->hasOne(LaporanPertanggungjawaban::class, 'surat_id');
    }

    // ── relasi ke documentapproval (log multi step) ────
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
        // cuma bs diedit klo status 'revised' (abis ditolak/perlu revisi) atau 'rejected_admin' (ditolak admin) atau 'pending_admin'
        return in_array($this->status, ['revised', 'rejected_admin', 'pending_admin']);
    }

    // ── helper: cek apakah bisa dihapus (oleh pembuat) ──
    public function canBeDeleted(): bool
    {
        if ($this->status === 'pending_admin' || $this->status === 'rejected_admin') {
            return true;
        }
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
