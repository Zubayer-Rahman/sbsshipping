<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'url',
        'method',
        'route_name',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function resolveAction(string $method, string $routeName = ''): string
    {
        if ($method === 'GET')    return 'visit';
        if ($method === 'POST')   return str_contains($routeName, 'store')   ? 'create' : 'action';
        if ($method === 'PUT' || $method === 'PATCH') return 'update';
        if ($method === 'DELETE') return 'delete';
        return 'action';
    }

    public static function resolveModule(string $url): string
    {
        $segments = explode('/', trim(parse_url($url, PHP_URL_PATH), '/'));
        return $segments[0] ?? 'general';
    }

    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'visit'  => 'Visited',
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            default  => 'Action',
        };
    }

    public static function actionColor(string $action): string
    {
        return match ($action) {
            'visit'  => '#1a56db',
            'create' => '#10b981',
            'update' => '#f59e0b',
            'delete' => '#ef4444',
            default  => '#64748b',
        };
    }
}
