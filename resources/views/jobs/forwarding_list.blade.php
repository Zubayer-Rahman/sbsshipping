@extends('layouts.app')
@section('title','Forwarding List')
@section('page-title','Forwarding List')
@section('breadcrumb','Jobs Manager / Forwarding List')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary);text-transform:uppercase;letter-spacing:.04em">
        Forwarding List
    </h2>
    <a href="{{ route('jobs.forwarding') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Create Letter
    </a>
</div>

<div class="card">
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
        <table style="width:100%;min-width:800px;border-collapse:collapse;font-size:13px">
            <thead>
                <tr>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap;width:50px">SL.</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap">Ref No</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap">Date</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap">Client</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap">Subject</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap">Jobs</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap;text-align:right">Total Amount</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap">Created</th>
                    <th style="background:#f0f4ff;padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);white-space:nowrap;text-align:center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                <tr style="border-bottom:1px solid var(--border);transition:background .12s"
                    onmouseover="this.style.background='#f8faff'"
                    onmouseout="this.style.background='transparent'">
                    <td style="padding:11px 14px;color:var(--text-muted)">
                        {{ $letters->total() - (($letters->currentPage()-1)*$letters->perPage()) - $loop->index }}
                    </td>
                    <td style="padding:11px 14px;font-weight:700;color:var(--primary)">
                        {{ $letter->ref_no ?? '—' }}
                    </td>
                    <td style="padding:11px 14px;white-space:nowrap">
                        {{ $letter->letter_date ? $letter->letter_date->format('d M Y') : '—' }}
                    </td>
                    <td style="padding:11px 14px;font-weight:600">
                        {{ $letter->contact ? $letter->contact->business_name : '—' }}
                    </td>
                    <td style="padding:11px 14px;font-size:12px;color:var(--text-muted)">
                        {{ $letter->subject ?? '—' }}
                    </td>
                    <td style="padding:11px 14px">
                        <span style="display:inline-flex;align-items:center;justify-content:center;
                                     background:var(--primary-light);color:var(--primary);
                                     width:28px;height:28px;border-radius:50%;
                                     font-size:12px;font-weight:700">
                            {{ count($letter->selected_job_ids ?? []) }}
                        </span>
                    </td>
                    <td style="padding:11px 14px;text-align:right;font-weight:700">
                        TK. {{ number_format($letter->total_amount, 2) }}
                    </td>
                    <td style="padding:11px 14px;color:var(--text-muted);font-size:12px;white-space:nowrap">
                        {{ $letter->created_at->format('d M Y, h:i A') }}
                    </td>
                    <td style="padding:11px 14px;text-align:center">
                        <div style="display:flex;gap:6px;justify-content:center">
                            <a href="{{ route('forwarding.preview', $letter) }}"
                               style="display:inline-flex;align-items:center;gap:4px;
                                      padding:5px 12px;border-radius:5px;
                                      background:#10b981;color:#fff;
                                      font-size:12px;font-weight:600;text-decoration:none">
                                <i class="bi bi-eye"></i> View PDF
                            </a>
                            <form method="POST" action="{{ route('forwarding.destroy', $letter) }}"
                                  onsubmit="return confirm('Delete this forwarding letter?')" style="margin:0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="display:inline-flex;align-items:center;gap:4px;
                                               padding:5px 12px;border-radius:5px;
                                               background:var(--danger);color:#fff;border:none;
                                               font-size:12px;font-weight:600;cursor:pointer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:52px;color:var(--text-muted)">
                        <i class="bi bi-file-earmark-text" style="font-size:42px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No forwarding letters yet.
                        <a href="{{ route('jobs.forwarding') }}" style="color:var(--primary)">Create your first one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($letters->hasPages())
    <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;
                flex-wrap:wrap;gap:8px;border-top:1px solid var(--border)">
        <div style="font-size:13px;color:var(--text-muted)">
            Showing {{ $letters->firstItem() }}–{{ $letters->lastItem() }} of {{ $letters->total() }} letters
        </div>
        {{ $letters->links() }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
nav[role="navigation"] { display:flex; align-items:center; justify-content:flex-end; }
.pagination { display:flex; gap:4px; list-style:none; margin:0; }
.pagination li a, .pagination li span {
    display:inline-flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:6px; font-size:13px; font-weight:600;
    border:1px solid var(--border); color:var(--text-muted); text-decoration:none; transition:all .15s;
}
.pagination li a:hover { border-color:var(--primary); color:var(--primary); }
.pagination li.active span { background:var(--primary); color:#fff; border-color:var(--primary); }
</style>
@endpush