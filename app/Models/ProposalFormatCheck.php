<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalFormatCheck extends Model
{
    public $timestamps = false;

    protected $table = 'proposal_format_checks';

    protected $fillable = [
        'surat_id',
        'skor_struktur',
        'skor_konten',
        'skor_akhir',
        'detail',
        'checked_at',
    ];

    protected $casts = [
        'detail' => 'array',
        'checked_at' => 'datetime',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }
}
