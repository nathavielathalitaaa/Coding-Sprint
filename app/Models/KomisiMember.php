<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomisiMember extends Model
{
    use HasFactory;

    protected $table = 'komisi_members';

    protected $fillable = [
        'user_id',
        'komisi_id',
    ];

    // ── Relasi ke User ─────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Relasi ke Komisi ───────────────────────────────
    public function komisi()
    {
        return $this->belongsTo(Komisi::class);
    }
}
