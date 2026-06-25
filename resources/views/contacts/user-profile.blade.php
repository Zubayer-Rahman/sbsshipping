@extends('layouts.app')

@section('title', $user->name . ' — Activity')
@section('page-title', 'User Profile')
@section('breadcrumb', 'Users / ' . $user->name)

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div style="display:flex;align-items:center;gap:16px">
        <div style="width:56px;height:56px;border-radius:50%;background:{{ $user->roleBadgeColor() }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:22px">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);margin-bottom:4px">
                {{ $user->name }}
                @if($user->id === Auth::id())
                <span style="font-size:12px;background:var(--primary-light);color:var(--primary);padding:2px 10px;border-radius:20px;font-weight:600;vertical-align:middle">You</span>
                @endif
            </h2>
            <div style="display:flex;align-items:center;gap:12px">
                <span style="font-size:13px;color:var(--text-muted)">{{ $user->email }}</span>
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;background:{{ $user->roleBadgeColor() }}20;color:{{ $user->roleBadgeColor() }}">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $user->roleBadgeColor() }};display:inline-block"></span>
                    {{ $user->roleLabel() }}
                </span>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        @if(Auth::user()->isSuperAdmin() && $user->id !== Auth::id())
        <form method="POST" action="{{ route('contacts.user.clear-log', $user) }}"
            onsubmit="return confirm('Clear all activity logs for {{ $user->name }}?')">
            @csrf
            <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#ef4444;border:none">
                <i class="bi bi-trash"></i> Clear Log
            </button>
        </form>
        @endif
        <a href="{{ route('contacts.user') }}" class="btn btn-outline btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- Last seen --}}
@if($lastActivity)
<div style="font-size:13px;color:var(--text-muted);margin-bottom:20px">
    <i class="bi bi-clock"></i>
    Last active: <strong>{{ $lastActivity->created_at->diffForHumans() }}</strong>
    — {{ $lastActivity->description }}
    <span style="margin-left:6px;font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:4px">{{ $lastActivity->ip_address }}</span>
</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:24px">
    @foreach([
    ['Total', $stats['total'], 'bi-activity', '#1a56db'],
    ['Visits', $stats['visits'], 'bi-eye', '#06b6d4'],
    ['Created', $stats['creates'],'bi-plus-circle', '#10b981'],
    ['Updated', $stats['updates'],'bi-pencil', '#f59e0b'],
    ['Deleted', $stats['deletes'],'bi-trash', '#ef4444'],
    ] as [$label, $val, $icon, $color])
    <div class="card" style="padding:16px;text-align:center">
        <i class="bi {{ $icon }}" style="font-size:20px;color:{{ $color }};margin-bottom:6px;display:block"></i>
        <div style="font-size:22px;font-weight:800;color:var(--text-primary)">{{ number_format($val) }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:2px">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Activity Log --}}
<div class="card">
    <div class="card-header" style="padding:18px 22px 14px">
        <span class="card-title"><i class="bi bi-journal-text" style="margin-right:8px;color:var(--primary)"></i>Activity Log</span>
        <span style="font-size:13px;color:var(--text-muted)">{{ $logs->total() }} total entries</span>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Module</th>
                        <th>URL</th>
                        <th>IP Address</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            @php $color = App\Models\ActivityLog::actionColor($log->action); @endphp
                            <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $color }}18;color:{{ $color }};text-transform:uppercase;letter-spacing:.04em">
                                <span style="width:5px;height:5px;border-radius:50%;background:{{ $color }};display:inline-block"></span>
                                {{ $log->action }}
                            </span>
                        </td>
                        <td style="font-size:13px;max-width:260px">{{ $log->description }}</td>
                        <td>
                            <span style="font-size:12px;background:var(--body-bg);padding:2px 8px;border-radius:4px;font-weight:600;color:var(--text-muted)">
                                {{ ucfirst(str_replace(['-','_'],' ',$log->module)) }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <span title="{{ $log->url }}">{{ parse_url($log->url, PHP_URL_PATH) }}</span>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $log->ip_address }}</td>
                        <td style="font-size:12px;color:var(--text-muted);white-space:nowrap">
                            <span title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">
                            <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
                            No activity recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="pagination-wrapper">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

@endsection