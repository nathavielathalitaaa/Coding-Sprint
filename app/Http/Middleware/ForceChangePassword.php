<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
public function handle($request, Closure $next)
{
    return $next($request);
}
}