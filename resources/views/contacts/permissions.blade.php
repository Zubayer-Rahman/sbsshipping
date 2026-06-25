@extends('layouts.app')

@section('title', 'Permission Management')
@section('page-title', 'Permission Management')
@section('breadcrumb', 'Users / Permissions')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);margin-bottom:4px">Permission Management</h2>
        <p style="font-size:13px;color:var(--text-muted)">Control which modules each role and user can access</p>
    </div>
    <a href="{{ route('contacts.user') }}" class="btn btn-outline btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

{{-- ── ROLE PERMISSIONS ── --}}
<div class="card" style="margin-bottom:24px">
    <div class="card-header" style="padding:18px 22px 14px">
        <span class="card-title"><i class="bi bi-people" style="margin-right:8px;color:var(--primary)"></i>Role-Level Permissions</span>
        <span style="font-size:12px;color:var(--text-muted)">Default access for all users of each role</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('permissions.role') }}">
            @csrf
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;min-width:600px">
                    <thead>
                        <tr>
                            <th style="text-align:left;padding:10px 16px;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;border-bottom:2px solid var(--border);background:var(--body-bg)">
                                Module
                            </th>
                            @foreach(['admin'=>['Admin','#1a56db'],'power_user'=>['Power User','#10b981'],'user'=>['User','#64748b']] as $role=>[$label,$color])
                            <th style="text-align:center;padding:10px 16px;font-size:12px;font-weight:700;border-bottom:2px solid var(--border);background:var(--body-bg)">
                                <span style="color:{{ $color }}">{{ $label }}</span>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $key => $label)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:12px 16px;font-weight:600;font-size:14px">
                                <i class="bi bi-{{ match($key) {
                                    'jobs' => 'briefcase',
                                    'expenses' => 'receipt',
                                    'bills' => 'file-earmark-text',
                                    'accounts' => 'wallet2',
                                    'contacts' => 'people',
                                    'ious' => 'cash-coin',
                                    'reports' => 'bar-chart-line',
                                    'additional-expenses' => 'plus-circle',
                                    'items' => 'box-seam',
                                    'purchases' => 'cart',
                                    default => 'grid'
                                } }}" style="margin-right:8px;color:var(--primary)"></i>
                                {{ $label }}
                            </td>
                            @foreach(['admin','power_user','user'] as $role)
                            <td style="text-align:center;padding:12px 16px">
                                <label style="display:inline-flex;align-items:center;cursor:pointer">
                                    <input type="checkbox"
                                        name="role_{{ $role }}_{{ $key }}"
                                        value="1"
                                        {{ ($rolePerms[$role][$key] ?? false) ? 'checked' : '' }}
                                        style="width:18px;height:18px;accent-color:var(--primary);cursor:pointer">
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:20px;display:flex;justify-content:flex-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Save Role Permissions
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── USER PERMISSIONS ── --}}
<div class="card">
    <div class="card-header" style="padding:18px 22px 14px">
        <span class="card-title"><i class="bi bi-person-gear" style="margin-right:8px;color:var(--primary)"></i>User-Level Overrides</span>
        <span style="font-size:12px;color:var(--text-muted)">Override role defaults for specific users</span>
    </div>
    <div class="card-body">

        <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:20px;font-size:13px;color:#92400e">
            <i class="bi bi-info-circle" style="margin-right:6px"></i>
            <strong>Inherit</strong> = use role default &nbsp;|&nbsp;
            <strong style="color:#10b981">Grant</strong> = always allow (even if role denies) &nbsp;|&nbsp;
            <strong style="color:#ef4444">Deny</strong> = always block (even if role allows)
        </div>

        @forelse($users as $user)
        <div class="card" style="margin-bottom:16px;border:1px solid var(--border)">
            <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                <div style="width:32px;height:32px;border-radius:50%;background:{{ $user->roleBadgeColor() }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:14px">{{ $user->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">{{ $user->email }} · {{ $user->roleLabel() }}</div>
                </div>
            </div>
            <div style="padding:16px 18px">
                <form method="POST" action="{{ route('permissions.user', $user) }}">
                    @csrf
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:16px">
                        @foreach($modules as $key => $label)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--body-bg)">
                            <span style="font-size:13px;font-weight:600">{{ $label }}</span>
                            <select name="user_{{ $user->id }}_{{ $key }}"
                                style="font-size:12px;padding:3px 8px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;color:var(--text-primary)">
                                <option value="inherit" {{ ($userPerms[$user->id][$key] ?? 'inherit')==='inherit'?'selected':'' }}>Inherit</option>
                                <option value="grant" {{ ($userPerms[$user->id][$key] ?? '')==='grant'?'selected':'' }} style="color:#10b981">Grant</option>
                                <option value="deny" {{ ($userPerms[$user->id][$key] ?? '')==='deny'?'selected':'' }} style="color:#ef4444">Deny</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                    <div style="display:flex;justify-content:flex-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-lg"></i> Save {{ $user->name }}'s Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <p style="text-align:center;color:var(--text-muted);padding:30px">No users to configure.</p>
        @endforelse
    </div>
</div>

@endsection