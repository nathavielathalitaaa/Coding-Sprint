<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTurunanTemplate extends Model
{
    protected $table = 'surat_turunan_templates';

    protected $fillable = [
        'kode',
        'nama',
        'konten_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── relasi ke instance surat turunan ───────────────
    public function suratTurunans()
    {
        return $this->hasMany(SuratTurunan::class);
    }

    // ── scope: hanya template aktif ────────────────────
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
