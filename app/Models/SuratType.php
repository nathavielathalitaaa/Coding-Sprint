<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratType extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'organisasi_tipe',
        'requires_kegiatan_detail',
        'nomor_format',
        'nomor_counter',
        'nomor_reset',
        'nomor_counter_reset_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'nomor_format'              => 'array',
        'is_active'                 => 'boolean',
        'requires_kegiatan_detail'  => 'boolean',
        'nomor_counter_reset_at'    => 'date',
    ];

    public function approvers()
    {
        return $this->hasMany(SuratTypeApprover::class)->orderBy('urutan');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }

    public function scopeForOrganisasiTipe($query, ?string $tipe)
    {
        return $query->where(function ($q) use ($tipe) {
            $q->where('organisasi_tipe', $tipe)
              ->orWhereNull('organisasi_tipe');
        });
    }
}
