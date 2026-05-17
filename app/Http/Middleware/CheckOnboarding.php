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
        $profile = $user->profile;

        // ── Cek apakah user terdaftar sebagai approver
        // di surat type manapun (berdasarkan user_id)
        $isApprover = SuratTypeApprover::where('user_id', $user->id)
            ->exists();

        // Bukan approver → skip onboarding, langsung masuk
        if (!$isApprover) {
            return $next($request);
        }

        // Approver → wajib punya TTD dan PIN
        if (!$profile) {
            return redirect()->route('onboarding');
        }

        $hasTtd = !empty($profile->ttd_path) 
               || !empty($profile->signature_path);
        $hasPin = !empty($profile->pin);

        if (!$hasTtd || !$hasPin) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
