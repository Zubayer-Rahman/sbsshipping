@extends('layouts.app')
@section('title', 'Job Groups')
@section('content')

<div style="padding:2rem;max-width:1400px;margin:0 auto;font-family:'Inter',sans-serif">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:1.875rem;font-weight:800;color:var(--text-primary);margin:0">Job Groups</h1>
            <p style="color:var(--text-muted);font-size:14px;margin:4px 0 0">Manage and track performance of related shipping jobs</p>
        </div>
        <a href="{{ route('job-groups.create') }}" style="background:var(--primary);color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px">+ New Group</a>
    </div>

    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-sm);border:1px solid var(--border)">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:var(--body-bg)">
                <tr>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em">Group Info</th>
                    <th style="padding:14px;text-align:center;font-size:11px;color:var(--text-muted);text-transform:uppercase">Total Jobs</th>
                    <th style="padding:14px;text-align:center;font-size:11px;color:var(--text-muted);text-transform:uppercase">Status</th>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);text-transform:uppercase">Created Date</th>
                    <th style="padding:14px;text-align:right;font-size:11px;color:var(--text-muted);text-transform:uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                <tr onclick="window.location='{{ route('job-groups.show', $group) }}'" style="border-bottom:1px solid var(--border);transition:background .15s;cursor:pointer" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background=''">
                    <td style="padding:14px">
                        <div style="font-weight:700;color:var(--primary);font-size:14px">{{ $group->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ $group->group_code }}</div>
                    </td>
                    <td style="padding:14px;text-align:center">
                        <span style="font-weight:700;color:var(--text-primary)">{{ $group->jobs_count }}</span>
                    </td>
                    <td style="padding:14px;text-align:center">
                        @php
                            $statusColors = ['active' => ['#d1fae5', '#065f46'], 'completed' => ['#dbeafe', '#1e40af'], 'archived' => ['#f1f5f9', '#64748b']];
                            $colors = $statusColors[$group->status] ?? ['#f1f5f9', '#64748b'];
                        @endphp
                        <span style="padding:4px 10px;border-radius:12px;background:{{ $colors[0] }};color:{{ $colors[1] }};font-size:10px;font-weight:700;text-transform:uppercase">{{ $group->status }}</span>
                    </td>
                    <td style="padding:14px;font-size:13px;color:var(--text-muted)">
                        {{ $group->created_at->format('d M Y') }}
                    </td>
                    <td style="padding:14px;text-align:right" onclick="event.stopPropagation()">
                        <a href="{{ route('job-groups.show', $group) }}" style="color:var(--primary);text-decoration:none;font-size:13px;font-weight:600;margin-right:12px">View</a>
                        <a href="{{ route('job-groups.edit', $group) }}" style="color:var(--success);text-decoration:none;font-size:13px;font-weight:600">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:40px;text-align:center;color:var(--text-muted)">No job groups found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection