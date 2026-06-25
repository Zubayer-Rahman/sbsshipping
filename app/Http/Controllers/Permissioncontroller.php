<?php

namespace App\Http\Controllers;

use App\Models\ModulePermission;
use App\Models\User;
use App\Models\UserModulePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    // ── Show permission management page ───────────────────────────────────────
    public function index()
    {
        if (!Auth::user()->isSuperAdmin()) abort(403);

        $modules = ModulePermission::allModules();
        $roles   = ['admin', 'power_user', 'user'];

        // Build role permissions matrix
        $rolePerms = [];
        foreach ($roles as $role) {
            foreach ($modules as $key => $label) {
                $rolePerms[$role][$key] = ModulePermission::where('role', $role)
                    ->where('module', $key)
                    ->where('allowed', true)
                    ->exists();
            }
        }

        // All non-super-admin users with their overrides
        $users = User::where('role', '!=', 'super_admin')
            ->orderBy('name')
            ->get();

        $userPerms = [];
        foreach ($users as $user) {
            foreach ($modules as $key => $label) {
                $override = UserModulePermission::where('user_id', $user->id)
                    ->where('module', $key)
                    ->first();
                $userPerms[$user->id][$key] = $override ? ($override->allowed ? 'grant' : 'deny') : 'inherit';
            }
        }

        return view('contacts.permissions', compact('modules', 'roles', 'rolePerms', 'users', 'userPerms'));
    }

    // ── Update role-level permissions ─────────────────────────────────────────
    public function updateRole(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) abort(403);

        $modules = array_keys(ModulePermission::allModules());
        $roles   = ['admin', 'power_user', 'user'];

        foreach ($roles as $role) {
            foreach ($modules as $module) {
                $allowed = $request->has("role_{$role}_{$module}");
                ModulePermission::updateOrCreate(
                    ['role' => $role, 'module' => $module],
                    ['allowed' => $allowed]
                );
            }
        }

        ModulePermission::clearCache();

        return back()->with('success', 'Role permissions updated successfully.');
    }

    // ── Update user-level permission override ─────────────────────────────────
    public function updateUser(Request $request, User $user)
    {
        if (!Auth::user()->isSuperAdmin()) abort(403);
        if ($user->isSuperAdmin()) abort(403, 'Cannot modify Super Admin permissions.');

        $modules = array_keys(ModulePermission::allModules());

        foreach ($modules as $module) {
            $value = $request->input("user_{$user->id}_{$module}", 'inherit');

            if ($value === 'inherit') {
                // Remove override — fall back to role default
                UserModulePermission::where('user_id', $user->id)
                    ->where('module', $module)
                    ->delete();
            } else {
                UserModulePermission::updateOrCreate(
                    ['user_id' => $user->id, 'module' => $module],
                    ['allowed' => $value === 'grant']
                );
            }
        }

        ModulePermission::clearCache();

        return back()->with('success', "{$user->name}'s permissions updated.");
    }
}
