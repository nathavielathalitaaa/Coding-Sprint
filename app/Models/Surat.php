<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surats';
    
    protected $fillable = [
        'user_id',
        'nomor_surat',
        'jenis_surat',
        'perihal',
        'file_pdf',
        'status',
        'approved_by_supervisor',
        'approved_by_owner',
        'catatan_revisi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
