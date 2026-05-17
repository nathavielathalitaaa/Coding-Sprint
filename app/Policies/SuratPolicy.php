<?php

namespace App\Policies;

use App\Models\Surat;
use App\Models\User;

class SuratPolicy
{
    // siapa saja bisa lihat list (difilter di controller)
    public function viewAny(User $user): bool
    {
        return true;
    }

    // lihat detail: staff hanya miliknya, approver sesuai jabatan, hr semua
    public function view(User $user, Surat $surat): bool
    {
        // pemilik selalu bisa lihat
        if ($user->id === $surat->user_id) {
            return true;
        }

        // supervisor/hr dengan jabatan approval bisa lihat semua surat
        if ($user->profile?->jabatan) {
            return true;
        }
        
        return $user->hasRole('hr');
    }

    // semua role bisa buat surat
    public function create(User $user): bool
    {
        return true;
    }

    public function store(User $user): bool
    {
        return true;
    }

    // hanya staff pemilik surat yang berstatus 'revised'
    public function edit(User $user, Surat $surat): bool
    {
        return $user->hasRole('staff')
            && $user->id === $surat->user_id
            && $surat->status === 'revised';
    }

    public function update(User $user, Surat $surat): bool
    {
        return $user->hasRole('staff')
            && $user->id === $surat->user_id
            && $surat->status === 'revised';
    }

    // download: staff hanya miliknya, siapapun dengan jabatan approval, hr semua
    public function download(User $user, Surat $surat): bool
    {
        // pemilik selalu bisa download
        if ($user->id === $surat->user_id) {
            return true;
        }

        if ($user->profile?->jabatan) {
            return true;
        }
        
        return $user->hasRole('hr');
    }
}