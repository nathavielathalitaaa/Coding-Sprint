<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Kirim notifikasi ke user tertentu.
     */
    public function send(int $userId, string $title, string $message, ?string $url = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'is_read' => false,
        ]);
    }

    /**
     * Kirim notifikasi ke semua user dengan jabatan tertentu.
     */
    public function sendToJabatan(string $jabatan, string $title, string $message, ?string $url = null)
    {
        // Sesuaikan dengan struktur baru (Role / organisasi_members)
        $users = collect();

        // 1. Cek di tabel users langsung by role (misal: kepala-sekolah, pengawas-pusat)
        $roleName = str_replace('_', '-', $jabatan); // kepala_sekolah -> kepala-sekolah
        
        $roleExists = \Spatie\Permission\Models\Role::where('name', $roleName)->exists();
        if ($roleExists) {
            $roleUsers = User::role($roleName)->get();
            if ($roleUsers->count() > 0) {
                $users = $users->concat($roleUsers);
            }
        }

        // 2. Cek di organisasi_members (untuk pembina, bph, ketua, dll)
        $orgUsers = User::whereHas('organisasiMembers', function($q) use ($jabatan) {
            $q->where('jabatan', $jabatan);
        })->get();
        if ($orgUsers->count() > 0) {
            $users = $users->concat($orgUsers);
        }

        // komisi_members tidak memiliki kolom jabatan, sehingga tidak perlu dicari di situ


        $users = $users->unique('id');

        foreach ($users as $user) {
            $this->send($user->id, $title, $message, $url);
        }
    }
}
