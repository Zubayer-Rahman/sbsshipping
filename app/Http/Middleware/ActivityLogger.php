<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    // Routes to skip logging (noise reduction)
    private array $skipRoutes = [
        'debugbar.*',
        'telescope.*',
        'horizon.*',
        'livewire.*',
    ];

    // URL patterns to skip
    private array $skipUrls = [
        '/debug',
        '/_debugbar',
        '/telescope',
        '/favicon',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log authenticated users
        if (!Auth::check()) {
            return $response;
        }

        // Skip certain URLs
        foreach ($this->skipUrls as $skip) {
            if (str_contains($request->path(), ltrim($skip, '/'))) {
                return $response;
            }
        }

        // Skip certain route names
        $routeName = $request->route()?->getName() ?? '';
        foreach ($this->skipRoutes as $skip) {
            if (fnmatch($skip, $routeName)) {
                return $response;
            }
        }

        // Skip failed responses (4xx/5xx) for GET — only log successful visits
        $statusCode = $response->getStatusCode();
        $method = $request->method();
        if ($method === 'GET' && $statusCode >= 400) {
            return $response;
        }

        $action = ActivityLog::resolveAction($method, $routeName);
        $module = ActivityLog::resolveModule($request->fullUrl());

        // Build a human-readable description
        $description = $this->buildDescription($action, $module, $routeName, $request);

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'module'     => $module,
            'url'        => $request->fullUrl(),
            'method'     => $method,
            'route_name' => $routeName ?: null,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $response;
    }

    private function buildDescription(string $action, string $module, string $routeName, Request $request): string
    {
        $module = ucfirst(str_replace(['-', '_'], ' ', $module));
        return match ($action) {
            'visit'  => "Visited {$module}",
            'create' => "Created a new {$module} record",
            'update' => "Updated a {$module} record",
            'delete' => "Deleted a {$module} record",
            default  => "Performed action on {$module}",
        };
    }
}
