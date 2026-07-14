<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles {
        HasRoles::hasRole as traitHasRole;
        HasRoles::hasAnyRole as traitHasAnyRole;
    }

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
        return $this->hasOne(EmployeeProfile::class, 'user_id');
    }

    /**
     * ambil profile, buat baru kalau belum ada.
     * pakai ini di controller/view supaya tidak null.
     * contoh: auth()->user()->profileornew->nik
     */
    public function getProfileOrNewAttribute(): EmployeeProfile
    {
        return $this->profile ?? new EmployeeProfile(['user_id' => $this->id]);
    }

    // ── relasi ke absensi 
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'user_id');
    }

    // ── helper: jabatan dari profile
    public function getJabatanAttribute(): ?string
    {
        return $this->profile?->jabatan;
    }

    // ── helper: cek jabatan untuk sistem approval
    public function hasJabatan(string $jabatan): bool
    {
        return $this->profile?->jabatan === $jabatan;
    }

    /**
     * Override HasRoles::hasRole to grant super-admin implicit access.
     */
    public function hasRole($roles, $guard = null): bool
    {
        if ($this->traitHasRole('super-admin')) {
            return true;
        }

        return $this->traitHasRole($roles, $guard);
    }

    /**
     * Override HasRoles::hasAnyRole to grant super-admin implicit access.
     */
    public function hasAnyRole($roles, $guard = null): bool
    {
        if ($this->traitHasAnyRole('super-admin')) {
            return true;
        }

        return $this->traitHasAnyRole($roles, $guard);
    }
}

