<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisasiMember extends Model
{
    use HasFactory;

    protected $table = 'organisasi_members';

    protected $fillable = [
        'user_id',
        'organisasi_id',
        'jabatan',
    ];

    // ── Relasi ke User ─────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Relasi ke Organisasi ───────────────────────────
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class);
    }

    // ── Label jabatan ──────────────────────────────────
    public function getJabatanLabelAttribute(): string
    {
        return match ($this->jabatan) {
            'anggota'   => 'Anggota',
            'sekretaris'=> 'Sekretaris',
            'ketua'     => 'Ketua',
            'bph'       => 'BPH',
            'komisi'    => 'Komisi',
            'pembina'   => 'Pembina',
            'pengawas'  => 'Pengawas',
            default     => ucfirst($this->jabatan),
        };
    }

    // ── Daftar jabatan valid ───────────────────────────
    public static function jabatanOptions(): array
    {
        return [
            'anggota'    => 'Anggota',
            'sekretaris' => 'Sekretaris',
            'ketua'      => 'Ketua',
            'bph'        => 'BPH (Badan Pengurus Harian)',
            'komisi'     => 'Komisi',
            'pembina'    => 'Pembina',
            'pengawas'   => 'Pengawas',
        ];
    }
}
