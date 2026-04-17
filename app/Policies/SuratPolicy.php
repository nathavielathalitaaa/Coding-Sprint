<?php

namespace App\Policies;

use App\Models\Surat;
use App\Models\User;

class SuratPolicy
{
    /**
     * Determine whether the user can view any surats.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the surat.
     */
    public function view(User $user, Surat $surat): bool
    {
        // Staff hanya boleh melihat surat miliknya sendiri
        if ($user->hasRole('staff')) {
            return $user->id === $surat->user_id;
        }

        // Supervisor boleh melihat semua surat dengan status submitted
        if ($user->hasRole('supervisor')) {
            return $surat->status === 'submitted';
        }

        // Admin boleh melihat semua surat
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create surats.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('staff');
    }

    /**
     * Determine whether the user can store surats.
     */
    public function store(User $user): bool
    {
        return $user->hasRole('staff');
    }

    /**
     * Determine whether the user can approve as supervisor.
     */
    public function approveSupervisor(User $user, Surat $surat): bool
    {
        return $user->hasRole('supervisor') && $surat->status === 'submitted';
    }

    /**
     * Determine whether the user can reject as supervisor.
     */
    public function rejectSupervisor(User $user, Surat $surat): bool
    {
        return $user->hasRole('supervisor') && $surat->status === 'submitted';
    }

    /**
     * Determine whether the user can approve as owner.
     */
    public function approveOwner(User $user, Surat $surat): bool
    {
        return $user->hasRole('admin') && $surat->status === 'approved_supervisor';
    }

    /**
     * Determine whether the user can reject as owner.
     */
    public function rejectOwner(User $user, Surat $surat): bool
    {
        return $user->hasRole('admin') && $surat->status === 'approved_supervisor';
    }

    /**
     * Determine whether the user can download surat.
     */
    public function download(User $user, Surat $surat): bool
    {
        // Staff hanya bisa download surat miliknya sendiri (semua status)
        if ($user->hasRole('staff')) {
            return $user->id === $surat->user_id;
        }

        // Supervisor bisa download semua surat yang pernah masuk tahap approvalnya (status >= submitted)
        if ($user->hasRole('supervisor')) {
            return in_array($surat->status, ['submitted', 'approved_supervisor', 'approved_owner', 'rejected', 'revised']);
        }

        // Admin bisa download semua surat
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can edit surat.
     */
    public function edit(User $user, Surat $surat): bool
    {
        return $user->hasRole('staff') && $user->id === $surat->user_id && $surat->status === 'revised';
    }

    /**
     * Determine whether the user can update surat.
     */
    public function update(User $user, Surat $surat): bool
    {
        return $user->hasRole('staff') && $user->id === $surat->user_id && $surat->status === 'revised';
    }
}
