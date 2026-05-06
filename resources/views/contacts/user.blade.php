@extends('layouts.app')
@section('title','User List')
@section('page-title','User List')
@section('breadcrumb','Contacts / User List')

@section('content')
<div class="table-card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email Address</th>
                    <th style="text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="user-name-cell">
                        {{ $user->name }}
                    </td>
                    <td class="user-email-cell">
                        {{ $user->email }}
                    </td>
                    <td style="text-align: right;">
                        <span class="status-badge">Active</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Inter', sans-serif;
    }

    .custom-table thead {
        background: var(--body-bg);
    }

    .custom-table th {
        padding: 14px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
    }

    .custom-table td {
        padding: 16px 20px;
        font-size: 14px;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover {
        background-color: var(--primary-light);
        transition: background 0.2s ease;
    }

    .user-name-cell {
        font-weight: 600;
        color: var(--primary) !important;
    }

    .user-email-cell {
        color: var(--text-muted);
    }

    .status-badge {
        background: #d1fae5;
        color: #065f46;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>
@endpush