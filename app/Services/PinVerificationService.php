<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * PinVerificationService
 * Mengelola verifikasi PIN user.
 * Digunakan oleh: SuratController, dll.
 */
class PinVerificationService
{
    /**
     * Verifikasi PIN user.
     * PIN disimpan sebagai bcrypt hash di tabel users
     */
    public function verify(User $user, string $pin): bool
    {
        if (!$user->pin) {
            return false;
        }

        return Hash::check($pin, $user->pin);
    }

    /**
     * Ambil path TTD user untuk disimpan sebagai snapshot.
     */
    public function getTtdPath(User $user): ?string
    {
        return $user->ttd_path;
    }
}
