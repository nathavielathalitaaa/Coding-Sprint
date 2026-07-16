<?php

namespace App\Policies;

use App\Models\OrganisasiMember;
use App\Models\Surat;
use App\Models\User;

class SuratPolicy
{
    // siapa saja bisa lihat list (difilter di controller)
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Lihat detail surat:
     * - Pemilik surat selalu bisa lihat
     * - Admin / super-admin bisa lihat semua
     * - OrganisasiMember dari organisasi pengaju surat bisa lihat
     * - User dengan role global (pengawas_pusat / kepala_sekolah) bisa lihat
     */
    public function view(User $user, Surat $surat): bool
    {
        // pemilik selalu bisa lihat
        if ($user->id === $surat->user_id) {
            return true;
        }

        // admin / super-admin bisa lihat semua
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        // role global bisa lihat semua
        if ($user->hasAnyRole(['pengawas_pusat', 'kepala_sekolah'])) {
            return true;
        }

        // anggota organisasi pengaju surat bisa lihat
        if ($surat->organisasi_id) {
            if (OrganisasiMember::where('user_id', $user->id)->where('organisasi_id', $surat->organisasi_id)->exists()) {
                return true;
            }
        }

        // cek apakah user adalah approver eksternal (misal MPK menyetujui surat OSIS)
        $userOrgTypes = \App\Models\Organisasi::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->pluck('tipe');

        $isApprover = \App\Models\DocumentApproval::where('document_type', 'surat_' . $surat->jenis_surat)
            ->where('document_id', $surat->id)
            ->where(function($q) use ($user, $userOrgTypes) {
                $q->where('assigned_user_id', $user->id)
                  ->orWhere('approver_id', $user->id);
                if ($userOrgTypes->contains('mpk')) $q->orWhere('target_mode', 'fixed_mpk');
                if ($userOrgTypes->contains('osis')) $q->orWhere('target_mode', 'fixed_osis');
            })->exists();

        if ($isApprover) {
            return true;
        }

        return false;
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

    /**
     * Edit surat: hanya pemilik surat ketika status 'revised'
     */
    public function edit(User $user, Surat $surat): bool
    {
        return $user->id === $surat->user_id
            && $surat->status === 'revised';
    }

    public function update(User $user, Surat $surat): bool
    {
        return $user->id === $surat->user_id
            && $surat->status === 'revised';
    }

    /**
     * Download surat:
     * - Pemilik surat selalu bisa download
     * - Admin bisa download semua
     * - OrganisasiMember dari organisasi pengaju bisa download
     * - Role global bisa download
     */
    public function download(User $user, Surat $surat): bool
    {
        // pemilik selalu bisa download
        if ($user->id === $surat->user_id) {
            return true;
        }

        // admin / super-admin bisa download semua
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        // role global bisa download
        if ($user->hasAnyRole(['pengawas_pusat', 'kepala_sekolah'])) {
            return true;
        }

        // anggota organisasi pengaju bisa download
        if ($surat->organisasi_id) {
            if (OrganisasiMember::where('user_id', $user->id)->where('organisasi_id', $surat->organisasi_id)->exists()) {
                return true;
            }
        }

        // cek apakah user adalah approver eksternal
        $userOrgTypes = \App\Models\Organisasi::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->pluck('tipe');

        $isApprover = \App\Models\DocumentApproval::where('document_type', 'surat_' . $surat->jenis_surat)
            ->where('document_id', $surat->id)
            ->where(function($q) use ($user, $userOrgTypes) {
                $q->where('assigned_user_id', $user->id)
                  ->orWhere('approver_id', $user->id);
                if ($userOrgTypes->contains('mpk')) $q->orWhere('target_mode', 'fixed_mpk');
                if ($userOrgTypes->contains('osis')) $q->orWhere('target_mode', 'fixed_osis');
            })->exists();

        if ($isApprover) {
            return true;
        }

        return false;
    }
}
