<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTurunanSigner extends Model
{
    protected $table = 'surat_turunan_signers';

    /**
     * Nilai valid untuk kolom status TTD.
     */
    const STATUS_WAITING = 'waiting';
    const STATUS_SIGNED  = 'signed';

    /**
     * Slot jabatan yang tersedia.
     * Dipakai untuk resolusi user aktual saat runtime.
     */
    const SLOT_KETUA_PELAKSANA = 'ketua_pelaksana';
    const SLOT_PEMBINA         = 'pembina';
    const SLOT_KEPALA_SEKOLAH  = 'kepala_sekolah';

    protected $fillable = [
        'surat_turunan_id',
        'user_id',
        'jabatan_slot',
        'status',
        'ttd_snapshot',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    // ── relasi ke surat turunan ────────────────────────
    public function suratTurunan()
    {
        return $this->belongsTo(SuratTurunan::class);
    }

    // ── relasi ke user penandatangan ───────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── helper: cek apakah slot ini sudah TTD ─────────
    public function sudahTtd(): bool
    {
        return $this->status === self::STATUS_SIGNED;
    }

    // ── label jabatan slot untuk tampilan ─────────────
    public function getJabatanLabelAttribute(): string
    {
        return match ($this->jabatan_slot) {
            self::SLOT_KETUA_PELAKSANA => 'Ketua Pelaksana',
            self::SLOT_PEMBINA         => 'Pembina',
            self::SLOT_KEPALA_SEKOLAH  => 'Kepala Sekolah',
            default                    => ucfirst(str_replace('_', ' ', $this->jabatan_slot)),
        };
    }
}
