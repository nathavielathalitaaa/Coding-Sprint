<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LaporanPertanggungjawaban extends Model
{
    use HasFactory;

    protected $table = 'laporan_pertanggungjawabans';

    protected $fillable = [
        'surat_id',
        'ringkasan_kegiatan',
        'realisasi_anggaran',
        'status',
        'catatan_revisi',
        'verified_by',
        'verified_at',
        'ttd_path',
        'keywords',
        'embedding_vector',
        'embedded_at',
        'archived_at',
    ];

    protected $casts = [
        'realisasi_anggaran' => 'array',
        'embedding_vector'   => 'array',
        'verified_at'        => 'datetime',
        'embedded_at'        => 'datetime',
        'archived_at'        => 'datetime',
    ];

    // ── Relationships ────────────────
    public function surat()
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function lpjLampirans()
    {
        return $this->hasMany(LpjLampiran::class, 'lpj_id');
    }

    // ── Search Scope ─────────────────
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            return $query->whereRaw('MATCH(keywords) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term]);
        }

        return $query->where('keywords', 'like', '%' . $term . '%');
    }
}
