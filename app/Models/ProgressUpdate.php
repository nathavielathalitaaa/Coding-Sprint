<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressUpdate extends Model
{
    use HasFactory;

    protected $table = 'progress_updates';

    protected $fillable = [
        'surat_id',
        'user_id',
        'persentase',
        'catatan',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
