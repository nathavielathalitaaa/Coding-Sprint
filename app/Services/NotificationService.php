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
        $users = User::whereHas('profile', function($q) use ($jabatan) {
            $q->where('jabatan', $jabatan);
        })->get();

        foreach ($users as $user) {
            $this->send($user->id, $title, $message, $url);
        }
    }
}
