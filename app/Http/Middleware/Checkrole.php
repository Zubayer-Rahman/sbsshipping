<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super admin always passes
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) return $next($request);
            if ($role === 'power_user' && $user->isPowerUser()) return $next($request);
            if ($role === $user->role) return $next($request);
        }

        abort(403, 'You do not have permission to access this page.');
    }
}
