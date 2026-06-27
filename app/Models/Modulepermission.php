<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ModulePermission extends Model
{
    protected $fillable = ['role', 'module', 'allowed'];

    protected $casts = ['allowed' => 'boolean'];

    // ── All available modules ─────────────────────────────────────────────────
    public static function allModules(): array
    {
        return [
            'jobs'                => 'Jobs Manager',
            'expenses'            => 'Expenses',
            'bills'               => 'Bills',
            'accounts'            => 'Accounts',
            'contacts'            => 'Contacts',
            'ious'                => 'IOU Management',
            'reports'             => 'Reports',
            'additional-expenses' => 'Additional Expenses',
            'items'               => 'Items',
            'purchases'           => 'Purchases',
            'salary'              => 'Salary'
        ];
    }

    // ── Check if a ROLE has access to a module ────────────────────────────────
    public static function roleCanAccess(string $role, string $module): bool
    {
        if ($role === 'super_admin') return true;

        return Cache::remember("role_perm_{$role}_{$module}", 300, function () use ($role, $module) {
            return static::where('role', $role)
                ->where('module', $module)
                ->where('allowed', true)
                ->exists();
        });
    }

    // ── Check if a USER has an individual override ────────────────────────────
    public static function userOverride(int $userId, string $module): ?bool
    {
        return Cache::remember("user_perm_{$userId}_{$module}", 300, function () use ($userId, $module) {
            $perm = UserModulePermission::where('user_id', $userId)
                ->where('module', $module)
                ->first();
            return $perm ? $perm->allowed : null;
        });
    }

    // ── Main permission check (user override → role default) ──────────────────
    public static function canAccess(object $user, string $module): bool
    {
        if ($user->role === 'super_admin') return true;

        // Check user-level override first
        $override = static::userOverride($user->id, $module);
        if ($override !== null) return $override;

        // Fall back to role-level permission
        return static::roleCanAccess($user->role, $module);
    }

    // ── Clear cache when permissions change ───────────────────────────────────
    public static function clearCache(string $role = null, int $userId = null): void
    {
        // Simple approach: clear all permission caches
        Cache::flush();
    }
}
