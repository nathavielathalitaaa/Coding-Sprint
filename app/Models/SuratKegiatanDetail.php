<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKegiatanDetail extends Model
{
    protected $table = 'surat_kegiatan_details';

    protected $fillable = [
        'surat_id',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'deskripsi_singkat',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ── relasi ke surat induk ──────────────────────────
    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }
}
