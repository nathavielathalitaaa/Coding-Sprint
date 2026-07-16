<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        // Jabatan struktural (label bebas, BUKAN untuk routing approval)
        // Approval routing 100% lewat OrganisasiMember->jabatan
        'jabatan_struktural',
        // Kontak
        'no_telepon',
        'tgl_bergabung',
        'alamat',
        // Approval signature & PIN
        'ttd_path',
        'signature_path',
        'pin',
    ];

    protected $hidden = [
        'pin', // jangan pernah expose PIN
    ];

    protected $casts = [
        'tgl_bergabung' => 'date',
    ];

    public function getJabatanAttribute()
    {
        return $this->jabatan_struktural;
    }

    public function setJabatanAttribute($value)
    {
        $this->attributes['jabatan_struktural'] = $value;
    }

    // ── Relasi ke User ─────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper: cek PIN ────────────────────────────────
    public function checkPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin);
    }

    // ── Helper: set PIN (auto hash) ────────────────────
    public function setPin(string $pin): void
    {
        $this->update(['pin' => Hash::make($pin)]);
    }
}
