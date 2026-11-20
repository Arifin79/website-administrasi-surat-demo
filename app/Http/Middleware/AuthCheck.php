<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('auth_user')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
