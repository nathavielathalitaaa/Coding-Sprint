<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'role_name',
        'status',
        'phone_number',
        'location',
        'join_date',
        'avatar',
        'position',    // kolom posisi singkat di users (opsional, bisa pakai profile)
        'department',
        'must_change_password',
        'ttd_path',
        'pin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'password'             => 'hashed',
        'join_date'            => 'date',
        'must_change_password' => 'boolean',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function organisasiMembers()
    {
        return $this->hasMany(OrganisasiMember::class, 'user_id');
    }

    public function komisiMembers()
    {
        return $this->hasMany(KomisiMember::class, 'user_id');
    }

    // ── helper: cek jabatan untuk sistem approval (mengambil dari salah satu keanggotaan)
    public function hasJabatan(string $jabatan): bool
    {
        return $this->organisasiMembers()->where('jabatan', $jabatan)->exists();
    }
}

