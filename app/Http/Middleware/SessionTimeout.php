<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    protected int $timeout = 120; // minutes

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');

            if ($lastActivity) {
                $inactiveMinutes = (time() - $lastActivity) / 60;

                if ($inactiveMinutes > $this->timeout) {
                    // Clear session and logout
                    $userName = Auth::user()->name;
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('session_expired', 
                            'Sesi Anda telah berakhir karena tidak aktif selama 2 jam. Silakan login kembali.');
                }
            }

            // Update last activity timestamp
            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
