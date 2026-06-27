@extends('layouts.app')

@section('title', 'Staff Management')
@section('page-title', 'Staff Management')
@section('breadcrumb', 'Salary / Staff')

@section('content')
<div class="card" style="margin-bottom:24px">
    <div class="card-header">
        <h2 class="card-title">Add New Staff Member</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('salary.staff.store') }}">
            @csrf
            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Rahim Uddin" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" class="form-control" placeholder="e.g. Driver" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Per Day Salary (৳)</label>
                    <input type="number" name="per_day_salary" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                </div>
            </div>
            <div style="margin-top:16px">
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Staff</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="padding-bottom:16px">
        <h2 class="card-title">All Staff</h2>
        <div style="display:flex;gap:10px">
            <a href="{{ route('salary.attendance') }}" class="btn btn-outline btn-sm"><i class="bi bi-calendar-check"></i> Attendance Sheet</a>
            <a href="{{ route('salary.sheet') }}" class="btn btn-primary btn-sm"><i class="bi bi-table"></i> Salary Sheet</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Per Day Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <span id="view-name-{{ $s->id }}">{{ $s->name }}</span>
                        <input type="text" id="edit-name-{{ $s->id }}" value="{{ $s->name }}" class="form-control" style="display:none;width:160px">
                    </td>
                    <td>
                        <span id="view-pos-{{ $s->id }}">{{ $s->position }}</span>
                        <input type="text" id="edit-pos-{{ $s->id }}" value="{{ $s->position }}" class="form-control" style="display:none;width:130px">
                    </td>
                    <td>
                        <span id="view-sal-{{ $s->id }}">৳ {{ number_format($s->per_day_salary, 2) }}</span>
                        <input type="number" id="edit-sal-{{ $s->id }}" value="{{ $s->per_day_salary }}" step="0.01" class="form-control" style="display:none;width:110px">
                    </td>
                    <td style="display:flex;gap:8px;align-items:center">
                        <button class="btn btn-outline btn-sm" onclick="startEdit({{ $s->id }})" id="edit-btn-{{ $s->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="saveEdit({{ $s->id }}, '{{ route('salary.staff.update', $s) }}')" id="save-btn-{{ $s->id }}" style="display:none">
                            <i class="bi bi-check-lg"></i> Save
                        </button>
                        <form method="POST" action="{{ route('salary.staff.destroy', $s) }}" onsubmit="return confirm('Remove {{ $s->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px">No staff added yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function startEdit(id) {
        ['name', 'pos', 'sal'].forEach(f => {
            document.getElementById(`view-${f}-${id}`).style.display = 'none';
            document.getElementById(`edit-${f}-${id}`).style.display = 'inline-block';
        });
        document.getElementById(`edit-btn-${id}`).style.display = 'none';
        document.getElementById(`save-btn-${id}`).style.display = 'inline-flex';
    }

    function saveEdit(id, url) {
        const name = document.getElementById(`edit-name-${id}`).value;
        const pos = document.getElementById(`edit-pos-${id}`).value;
        const sal = document.getElementById(`edit-sal-${id}`).value;

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({
                name,
                position: pos,
                per_day_salary: sal
            })
        }).then(r => r.json()).then(() => {
            document.getElementById(`view-name-${id}`).textContent = name;
            document.getElementById(`view-pos-${id}`).textContent = pos;
            document.getElementById(`view-sal-${id}`).textContent = '৳ ' + parseFloat(sal).toFixed(2);
            ['name', 'pos', 'sal'].forEach(f => {
                document.getElementById(`view-${f}-${id}`).style.display = '';
                document.getElementById(`edit-${f}-${id}`).style.display = 'none';
            });
            document.getElementById(`edit-btn-${id}`).style.display = '';
            document.getElementById(`save-btn-${id}`).style.display = 'none';
        });
    }
</script>
@endpush