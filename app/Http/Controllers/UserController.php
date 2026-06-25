<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    // ── User List ─────────────────────────────────────────────────────────────
    public function index()
    {
        $users = User::withCount('activityLogs')
            ->orderByRaw("FIELD(role, 'super_admin', 'admin', 'power_user', 'user')")
            ->get();

        return view('contacts.user', compact('users'));
    }

    // ── Create User ───────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|in:user,power_user,admin,super_admin',
        ]);

        if (in_array($request->role, ['super_admin', 'admin']) && !Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can create Admin or Super Admin accounts.');
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        if ($request->boolean('send_welcome')) {
            try {
                Mail::raw(
                    "Hello {$user->name},\n\nYour account has been created.\n\nEmail: {$user->email}\nPassword: {$request->password}\n\nPlease login and change your password.\n\nSBS Shipping",
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Welcome to SBS Shipping — Your Account Details');
                    }
                );
            } catch (\Exception $e) {
            }
        }

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'create',
            'module'      => 'users',
            'url'         => $request->fullUrl(),
            'method'      => 'POST',
            'route_name'  => 'contacts.user.store',
            'description' => "Created new user: {$user->name} ({$user->roleLabel()})",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('success', "User {$user->name} created successfully.");
    }

    // ── Delete User ───────────────────────────────────────────────────────────
    public function destroy(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can delete users.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete a Super Admin account.');
        }

        $name = $user->name;
        $user->activityLogs()->delete();
        $user->delete();

        return back()->with('success', "User {$name} has been deleted.");
    }

    // ── User Profile + Activity Log ───────────────────────────────────────────
    public function show(User $user)
    {
        $authUser = Auth::user();
        if (!$authUser->isAdmin() && $authUser->id !== $user->id) abort(403);

        $logs         = $user->activityLogs()->latest()->paginate(25);
        $stats        = [
            'total'   => $user->activityLogs()->count(),
            'visits'  => $user->activityLogs()->where('action', 'visit')->count(),
            'creates' => $user->activityLogs()->where('action', 'create')->count(),
            'updates' => $user->activityLogs()->where('action', 'update')->count(),
            'deletes' => $user->activityLogs()->where('action', 'delete')->count(),
        ];
        $lastActivity = $user->activityLogs()->latest()->first();

        return view('contacts.user-profile', compact('user', 'logs', 'stats', 'lastActivity'));
    }

    // ── Update Role ───────────────────────────────────────────────────────────
    public function updateRole(Request $request, User $user)
    {
        if (!Auth::user()->isSuperAdmin()) abort(403, 'Only Super Admin can change roles.');
        if ($user->id === Auth::id()) return back()->with('error', 'You cannot change your own role.');

        $request->validate(['role' => 'required|in:user,power_user,admin,super_admin']);

        $oldRole = $user->roleLabel();
        $user->update(['role' => $request->role]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'update',
            'module'      => 'users',
            'url'         => $request->fullUrl(),
            'method'      => 'POST',
            'route_name'  => 'contacts.user.role',
            'description' => "Changed {$user->name}'s role from {$oldRole} to {$user->roleLabel()}",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('success', "{$user->name}'s role updated to {$user->roleLabel()}.");
    }

    // ── Clear Log ─────────────────────────────────────────────────────────────
    public function clearLog(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) abort(403);
        $user->activityLogs()->delete();
        return back()->with('success', "Activity log cleared for {$user->name}.");
    }
}
