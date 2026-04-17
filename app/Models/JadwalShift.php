<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalShift extends Model
{
    use HasFactory;

    // kolom yang boleh diisi massal
    protected $fillable = [
        'user_id',
        'shift_id',
        'tanggal_mulai',
        'tanggal_selesai',
    ];
}
