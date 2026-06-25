<?php

namespace App\Http\Middleware;

use App\Models\ModulePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!ModulePermission::canAccess($user, $module)) {
            abort(403, 'You do not have access to this module. Contact your Super Admin.');
        }

        return $next($request);
    }
}
