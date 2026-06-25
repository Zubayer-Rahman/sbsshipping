@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('breadcrumb', 'Contacts / Users')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);margin-bottom:4px">User Management</h2>
        <p style="font-size:13px;color:var(--text-muted)">Create, manage roles and monitor user activity</p>
    </div>
    @if(Auth::user()->isAdmin())
    <div style="display:flex;gap:10px">
        <a href="{{ route('permissions.index') }}" class="btn btn-outline">
            <i class="bi bi-shield-check"></i> Manage Permissions
        </a>
        <button onclick="document.getElementById('createUserModal').style.display='flex'" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Add User
        </button>
    </div>
    @endif
</div>

{{-- Role legend --}}
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    @foreach(['super_admin'=>['Super Admin','#ef4444'],'admin'=>['Admin','#1a56db'],'power_user'=>['Power User','#10b981'],'user'=>['User','#64748b']] as $key=>[$label,$color])
    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted)">
        <span style="width:10px;height:10px;border-radius:50%;background:{{ $color }};display:inline-block"></span>
        <span>{{ $label }}</span>
    </div>
    @endforeach
</div>

{{-- User Table --}}
<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Activity</th>
                        @if(Auth::user()->isSuperAdmin())
                        <th>Change Role</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom:1px solid var(--border)">
                        <td style="color:var(--text-muted);font-size:12px">{{ $loop->iteration }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:34px;height:34px;border-radius:50%;background:{{ $user->roleBadgeColor() }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                <span style="font-weight:600">{{ $user->name }}</span>
                                @if($user->id === Auth::id())
                                <span style="font-size:11px;background:var(--primary-light);color:var(--primary);padding:2px 8px;border-radius:20px;font-weight:600">You</span>
                                @endif
                            </div>
                        </td>
                        <td style="color:var(--text-muted)">{{ $user->email }}</td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $user->roleBadgeColor() }}20;color:{{ $user->roleBadgeColor() }}">
                                <span style="width:6px;height:6px;border-radius:50%;background:{{ $user->roleBadgeColor() }};display:inline-block"></span>
                                {{ $user->roleLabel() }}
                            </span>
                        </td>
                        <td style="font-size:13px;color:var(--text-muted)">
                            {{ number_format($user->activity_logs_count) }} actions
                        </td>

                        @if(Auth::user()->isSuperAdmin())
                        <td>
                            @if($user->id !== Auth::id())
                            <form method="POST" action="{{ route('contacts.user.role', $user) }}" style="display:flex;gap:8px;align-items:center">
                                @csrf
                                <select name="role" class="form-control" style="padding:5px 10px;font-size:12px;width:auto">
                                    @foreach(App\Models\User::availableRoles() as $value => $label)
                                    <option value="{{ $value }}" {{ $user->role===$value?'selected':'' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </form>
                            @else
                            <span style="font-size:12px;color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        @endif

                        <td>
                            <div style="display:flex;gap:6px;align-items:center">
                                <a href="{{ route('contacts.user.show', $user) }}" class="btn btn-outline btn-sm">
                                    <i class="bi bi-activity"></i> Activity
                                </a>
                                @if(Auth::user()->isSuperAdmin() && $user->id !== Auth::id() && !$user->isSuperAdmin())
                                <form method="POST" action="{{ route('contacts.user.destroy', $user) }}"
                                    onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#ef4444;border:none">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create User Modal --}}
@if(Auth::user()->isAdmin())
<div id="createUserModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:var(--radius);width:100%;max-width:480px;padding:28px;position:relative;max-height:90vh;overflow-y:auto">
        <button onclick="document.getElementById('createUserModal').style.display='none'"
            style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted)">
            <i class="bi bi-x-lg"></i>
        </button>
        <h3 style="font-size:17px;font-weight:800;margin-bottom:20px;color:var(--text-primary)">
            <i class="bi bi-person-plus" style="color:var(--primary);margin-right:8px"></i>Create New User
        </h3>

        <form method="POST" action="{{ route('contacts.user.store') }}">
            @csrf

            <div class="form-group" style="margin-bottom:16px">
                <label class="form-label">Full Name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="John Doe">
            </div>

            <div class="form-group" style="margin-bottom:16px">
                <label class="form-label">Email Address <span style="color:var(--danger)">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="john@example.com">
            </div>

            <div class="form-group" style="margin-bottom:16px">
                <label class="form-label">Password <span style="color:var(--danger)">*</span></label>
                <input type="password" name="password" class="form-control" required placeholder="Min. 8 characters">
            </div>

            <div class="form-group" style="margin-bottom:16px">
                <label class="form-label">Confirm Password <span style="color:var(--danger)">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
            </div>

            <div class="form-group" style="margin-bottom:16px">
                <label class="form-label">Role <span style="color:var(--danger)">*</span></label>
                <select name="role" class="form-control" required>
                    @foreach(App\Models\User::availableRoles() as $value => $label)
                    @if(!($value === 'super_admin' && !Auth::user()->isSuperAdmin()))
                    <option value="{{ $value }}" {{ old('role')===$value?'selected':'' }}>{{ $label }}</option>
                    @endif
                    @endforeach
                </select>
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
                <input type="checkbox" name="send_welcome" id="send_welcome" value="1" style="width:16px;height:16px;accent-color:var(--primary)">
                <label for="send_welcome" style="font-size:13px;color:var(--text-primary);cursor:pointer">
                    Send welcome email with login details
                </label>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="document.getElementById('createUserModal').style.display='none'" class="btn btn-outline">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-check"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reopen modal on validation error --}}
@if($errors->any())
<script>
    document.getElementById('createUserModal').style.display = 'flex';
</script>
@endif
@endif

@endsection