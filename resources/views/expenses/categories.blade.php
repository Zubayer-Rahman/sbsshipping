@extends('layouts.app')
@section('title','Expense Categories')
@section('page-title','Expense Categories')
@section('breadcrumb','Expenses / Expense Categories')

@section('content')

<div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;flex-wrap:wrap;justify-content:space-between">
    <div>
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Expense Categories
        </h2>
        <span style="font-size:13px;color:var(--text-muted)">Manage your expense categories</span>
    </div>
</div>

{{-- Add Category Modal Trigger --}}
<div class="card" style="margin-bottom:20px">
    <div style="padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
        <span style="font-size:14px;font-weight:700">All your expense categories</span>
        <button onclick="document.getElementById('addCatModal').style.display='flex'"
            style="display:inline-flex;align-items:center;gap:6px;padding:8px 20px;
                       border-radius:30px;background:#7c3aed;color:#fff;border:none;
                       font-size:13px;font-weight:700;cursor:pointer">
            <i class="bi bi-plus-lg"></i> Add
        </button>
    </div>

    {{-- Toolbar --}}
    <div style="padding:10px 20px 10px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:13px;color:var(--text-muted)">Show</span>
            <form method="GET" action="{{ route('expense-categories.list') }}" id="ppForm2">
                <select name="per_page" class="form-select" style="width:70px;padding:5px 8px;font-size:13px"
                    onchange="document.getElementById('ppForm2').submit()">
                    @foreach([10,25,50,100] as $pp)
                    <option value="{{ $pp }}" {{ request('per_page',50)==$pp?'selected':'' }}>{{ $pp }}</option>
                    @endforeach
                </select>
            </form>
            <span style="font-size:13px;color:var(--text-muted)">entries</span>
        </div>
        <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center">
            @foreach(['Export CSV'=>'bi-filetype-csv','Export Excel'=>'bi-file-earmark-excel','Print'=>'bi-printer','Column visibility'=>'bi-layout-three-columns','Export PDF'=>'bi-filetype-pdf'] as $lbl=>$ic)
            <button type="button" class="exp-toolbar-btn"><i class="bi {{ $ic }}"></i> <span class="hide-sm">{{ $lbl }}</span></button>
            @endforeach
            <input type="text" id="catSearch" placeholder="Search ..."
                class="form-control" style="width:160px;padding:6px 12px;font-size:13px">
        </div>
    </div>

    <div style="overflow-x:auto">
        <table style="width:100%;min-width:600px;border-collapse:collapse;font-size:13px" id="catTable">
            <thead>
                <tr style="background:#f0f4ff">
                    <th style="padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid var(--border)">
                        Category name <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i>
                    </th>
                    <th style="padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid var(--border)">
                        Category code <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i>
                    </th>
                    <th style="padding:11px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid var(--border)">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody id="catBody">
                @forelse($categories as $cat)
                <tr style="border-bottom:1px solid var(--border)"
                    onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background=''">
                    <td style="padding:10px 14px;font-weight:500">
                        {{ $cat->name }}
                        @if($cat->parent_category)
                        <span style="font-size:11px;color:var(--text-muted);margin-left:6px">({{ $cat->parent_category }})</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;color:var(--text-muted)">{{ $cat->code ?? '—' }}</td>
                    <td style="padding:10px 14px">
                        <div style="display:flex;gap:6px">
                            <button onclick="openEditCat({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->code }}', '{{ $cat->parent_category }}')"
                                style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;background:var(--primary-light);color:var(--primary);border:1px solid var(--primary);font-size:12px;font-weight:600;cursor:pointer">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('expense-categories.destroy', $cat) }}"
                                onsubmit="return confirm('Delete this category?')" style="margin:0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;background:#fee2e2;color:var(--danger);border:1px solid var(--danger);font-size:12px;font-weight:600;cursor:pointer">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center;padding:48px;color:var(--text-muted)">
                        No categories yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
    <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-muted)">Showing {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }}</span>
        {{ $categories->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ── Add Modal ── --}}
<div id="addCatModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:28px;width:460px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h3 style="font-family:'Syne',sans-serif;font-size:17px;font-weight:800">Add Category</h3>
            <button onclick="document.getElementById('addCatModal').style.display='none'"
                style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-muted)">&times;</button>
        </div>
        <form method="POST" action="{{ route('expense-categories.store') }}">
            @csrf
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Category Name:<span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Documentation" required>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Category Code:</label>
                <input type="text" name="code" class="form-control" placeholder="Optional code">
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label class="form-label">Parent Category:<span style="color:var(--danger)">*</span></label>
                <select name="parent_category" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="Job Expense">Job Expense</option>
                    <option value="Office Expense">Office Expense</option>
                </select>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="document.getElementById('addCatModal').style.display='none'"
                    class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Modal ── --}}
<div id="editCatModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:28px;width:460px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h3 style="font-family:'Syne',sans-serif;font-size:17px;font-weight:800">Edit Category</h3>
            <button onclick="document.getElementById('editCatModal').style.display='none'"
                style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-muted)">&times;</button>
        </div>
        <form id="editCatForm" method="POST">
            @csrf @method('PUT')
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Category Name:<span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" id="editCatName" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Category Code:</label>
                <input type="text" name="code" id="editCatCode" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label class="form-label">Parent Category:<span style="color:var(--danger)">*</span></label>
                <select name="parent_category" id="editCatParent" class="form-select" required>
                    <option value="Job Expense">Job Expense</option>
                    <option value="Office Expense">Office Expense</option>
                </select>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="document.getElementById('editCatModal').style.display='none'"
                    class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .exp-toolbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 5px;
        background: #f8fafc;
        border: 1px solid var(--border);
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
        cursor: pointer;
        transition: all .15s;
    }

    .exp-toolbar-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    nav[role="navigation"] {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        margin: 0;
    }

    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        transition: all .15s;
    }

    .pagination li a:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .pagination li.active span {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    @media(max-width:640px) {
        .hide-sm {
            display: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function openEditCat(id, name, code, parent) {
        document.getElementById('editCatForm').action = '/expense-categories/' + id;
        document.getElementById('editCatName').value = name;
        document.getElementById('editCatCode').value = code || '';
        document.getElementById('editCatParent').value = parent || 'Job Expense';
        document.getElementById('editCatModal').style.display = 'flex';
    }
    document.getElementById('catSearch').addEventListener('input', function() {
        const v = this.value.toLowerCase();
        document.querySelectorAll('#catBody tr').forEach(r => r.style.display = r.textContent.toLowerCase().includes(v) ? '' : 'none');
    });
</script>
@endpush