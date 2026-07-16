<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komisi extends Model
{
    use HasFactory;

    protected $table = 'komisis';

    protected $fillable = [
        'nama',
        'organisasi_id',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relasi ke Organisasi (MPK) ─────────────────────
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class);
    }

    // ── Relasi ke anggota komisi ───────────────────────
    public function members()
    {
        return $this->hasMany(KomisiMember::class);
    }

    // ── Relasi ke surat yang terkait ───────────────────
    public function surats()
    {
        return $this->hasMany(Surat::class);
    }

    // ── Scope: hanya komisi aktif ─────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
