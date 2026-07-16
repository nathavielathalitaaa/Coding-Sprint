<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTurunan extends Model
{
    protected $table = 'surat_turunans';

    /**
     * Nilai valid untuk kolom status.
     * Dipakai untuk validasi dan referensi di seluruh codebase.
     */
    const STATUS_DRAFT         = 'draft';
    const STATUS_MENUNGGU_TTD  = 'menunggu_ttd';
    const STATUS_DITANDATANGANI = 'ditandatangani';

    protected $fillable = [
        'surat_id',
        'surat_turunan_template_id',
        'nomor_surat',
        'konten_final',
        'file_pdf_path',
        'status',
        'created_by',
    ];

    // ── relasi ke surat induk ──────────────────────────
    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    // ── relasi ke template yang digunakan ─────────────
    public function template()
    {
        return $this->belongsTo(SuratTurunanTemplate::class, 'surat_turunan_template_id');
    }

    // ── relasi ke daftar signer ────────────────────────
    public function signers()
    {
        return $this->hasMany(SuratTurunanSigner::class);
    }

    // ── relasi ke user pembuat (sekretaris) ───────────
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── helper: cek apakah semua signer sudah TTD ─────
    /**
     * Mengembalikan true jika SEMUA baris SuratTurunanSigner
     * terkait berstatus 'signed'. Juga mengembalikan false
     * jika belum ada signer sama sekali (guard untuk edge case).
     */
    public function semuaSignerSudahTtd(): bool
    {
        // Tidak ada signer → belum bisa dianggap selesai
        if ($this->signers()->doesntExist()) {
            return false;
        }

        return $this->signers()
                    ->where('status', '!=', SuratTurunanSigner::STATUS_SIGNED)
                    ->doesntExist();
    }

    // ── label status untuk tampilan ────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT          => 'Draft',
            self::STATUS_MENUNGGU_TTD   => 'Menunggu TTD',
            self::STATUS_DITANDATANGANI => 'Ditandatangani',
            default                     => ucfirst($this->status),
        };
    }

    // ── warna badge per status ─────────────────────────
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT          => 'b-gray',
            self::STATUS_MENUNGGU_TTD   => 'b-amber',
            self::STATUS_DITANDATANGANI => 'b-green',
            default                     => 'b-gray',
        };
    }
}
