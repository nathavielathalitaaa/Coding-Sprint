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

        // Admin/super-admin tidak seharusnya menjadi signer ttd & bebas onboarding
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return $next($request);
        }

        // Cek apakah user terdaftar sebagai signer
        $isSigner = SuratTypeApprover::where('user_id', $user->id)
            ->where('is_signer', true)
            ->exists();

        $hasJabatanSigner = false;
        $userJabatans = $user->organisasiMembers()->pluck('jabatan')->filter()->unique()->toArray();
        if (in_array('bph', $userJabatans)) {
            $userJabatans[] = 'bph_osis';
            $userJabatans[] = 'bph_mpk';
        }
        if (!empty($userJabatans)) {
            $hasJabatanSigner = SuratTypeApprover::whereIn('jabatan_label', $userJabatans)
                ->where('is_signer', true)
                ->exists();
        }

        // Bukan signer → skip onboarding, langsung masuk
        if (!$isSigner && !$hasJabatanSigner) {
            return $next($request);
        }

        // Signer → wajib punya TTD dan PIN
        $hasTtd = !empty($user->ttd_path);
        $hasPin = !empty($user->pin);

        if (!$hasTtd || !$hasPin) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
