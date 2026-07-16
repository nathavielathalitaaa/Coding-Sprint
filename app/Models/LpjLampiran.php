<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpjLampiran extends Model
{
    use HasFactory;

    protected $table = 'lpj_lampirans';

    protected $fillable = [
        'lpj_id',
        'file_path',
        'tipe',
        'keterangan',
    ];

    public function lpj()
    {
        return $this->belongsTo(LaporanPertanggungjawaban::class, 'lpj_id');
    }
}
