<?php

namespace App\Policies;

use App\Models\Surat;
use App\Models\User;

class SuratPolicy
{
    // Semua role bisa lihat daftar surat
    public function viewAny(User $user): bool
    {
        return true;
    }

    // Lihat detail: Pemilik, BPH yang bertugas, atau Pembina (Super Admin)
    public function view(User $user, Surat $surat): bool
    {
        return $user->id === $surat->user_id || 
               $user->hasRole('pembina') || 
               $surat->approvals()->where('assigned_user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function store(User $user): bool
    {
        return true;
    }

    // Hanya anggota pemilik surat yang berstatus 'revised' yang bisa edit
    public function edit(User $user, Surat $surat): bool
    {
        return $user->hasRole('anggota') 
            && $user->id === $surat->user_id 
            && $surat->status === 'revised';
    }

    public function update(User $user, Surat $surat): bool
    {
        return $user->hasRole('anggota') 
            && $user->id === $surat->user_id 
            && $surat->status === 'revised';
    }

    // Download: Pemilik, BPH yang bertugas, atau Pembina
    public function download(User $user, Surat $surat): bool
    {
        return $user->id === $surat->user_id || 
               $user->hasRole('pembina') || 
               $surat->approvals()->where('assigned_user_id', $user->id)->exists();
    }
}