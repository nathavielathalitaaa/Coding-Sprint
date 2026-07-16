<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentApproval extends Model
{
    protected $fillable = [
        'document_type',
        'document_id',
        'step_order',
        'jabatan',
        'target_mode',
        'surat_organisasi_id',
        'surat_komisi_id',
        'assigned_user_id', // user spesifik yang harus approve step ini (nullable)
        'label',
        'metode_ttd',
        'is_signer',
        'approver_id',
        'ttd_snapshot',
        'cover_pdf_path',
        'status',
        'is_read',
        'catatan',
        'actioned_at',
    ];

    protected $casts = [
        'is_signer'   => 'boolean',
        'actioned_at' => 'datetime',
    ];

    // ── Relasi ─────────────────────────────────────────
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Relasi ke user yang ditunjuk secara spesifik untuk approve step ini.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    // ── Scope: step yang sedang menunggu aksi ──────────
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    // ── Scope: untuk dokumen tertentu ─────────────────
    public function scopeForDocument($query, string $type, int $id)
    {
        return $query->where('document_type', $type)
                     ->where('document_id', $id)
                     ->orderBy('step_order');
    }
}
