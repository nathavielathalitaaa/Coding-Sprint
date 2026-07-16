<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            auth()->user()->must_change_password === true
        ) {
            // Routes yang diizinkan: profile, logout, password update
            $allowedRoutes = [
                'profile.show',
                'profile.password',
                'logout',
            ];

            $allowedPrefixes = [
                'account/profile',
                'profile/password',
                'logout',
            ];

            // Cek apakah route saat ini termasuk yang diizinkan
            $currentRoute = $request->route()?->getName();
            if (in_array($currentRoute, $allowedRoutes)) {
                return $next($request);
            }

            // Cek berdasarkan URL prefix
            foreach ($allowedPrefixes as $prefix) {
                if ($request->is($prefix)) {
                    return $next($request);
                }
            }

            flash()->warning('Anda diwajibkan mengganti password sebelum melanjutkan.');
            return redirect()->route('profile.show');
        }

        return $next($request);
    }
}
