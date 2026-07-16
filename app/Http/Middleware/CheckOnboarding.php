<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SuratTypeApprover;

class CheckOnboarding
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        // ── Cek apakah user terdaftar sebagai approver
        // di surat type manapun (berdasarkan user_id)
        $isApprover = SuratTypeApprover::where('user_id', $user->id)
            ->exists();

        // Bisa juga cek berdasarkan jabatan jika ada di SuratTypeApprover
        $hasJabatanApprover = false;
        $userJabatans = $user->organisasiMembers()->pluck('jabatan')->filter()->unique();
        if ($userJabatans->isNotEmpty()) {
            $hasJabatanApprover = SuratTypeApprover::whereIn('jabatan_label', $userJabatans)->exists();
        }

        // Bukan approver → skip onboarding, langsung masuk
        if (!$isApprover && !$hasJabatanApprover) {
            return $next($request);
        }

        // Approver → wajib punya TTD dan PIN
        $hasTtd = !empty($user->ttd_path);
        $hasPin = !empty($user->pin);

        if (!$hasTtd || !$hasPin) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
