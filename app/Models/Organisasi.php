<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisasi extends Model
{
    use HasFactory;

    protected $table = 'organisasis';

    protected $fillable = [
        'nama',
        'tipe',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relasi ke anggota ──────────────────────────────
    public function members()
    {
        return $this->hasMany(OrganisasiMember::class);
    }

    // ── Relasi ke komisi (khusus MPK) ─────────────────
    public function komisis()
    {
        return $this->hasMany(Komisi::class);
    }

    // ── Relasi ke surat yang diajukan ─────────────────
    public function surats()
    {
        return $this->hasMany(Surat::class);
    }

    // ── Label tipe ────────────────────────────────────
    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe) {
            'osis'      => 'OSIS',
            'mpk'       => 'MPK',
            'sub_organ' => 'Sub Organ',
            default     => ucfirst($this->tipe),
        };
    }

    // ── Helper: ambil anggota berdasarkan jabatan ─────
    public function membersByJabatan(string $jabatan)
    {
        return $this->members()->where('jabatan', $jabatan)->with('user')->get();
    }

    // ── Scope: hanya organisasi aktif ────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
