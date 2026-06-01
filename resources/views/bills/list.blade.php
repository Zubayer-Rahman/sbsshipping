@extends('layouts.app')
@section('title', 'Bills')
@section('content')

<style>
    .container {
        max-width: 100%;
    }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Bills</h1>
        <a href="{{ route('bills.create') }}" class="btn btn-primary">Create Bill</a>
    </div>

    <form method="GET" action="{{ route('bills.list') }}" class="mb-3">
        <div class="input-group">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search bills by number, customer, or reference">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    @if($bills->count())
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bill No</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                <tr>
                    <td>{{ $loop->iteration + ($bills->currentPage()-1) * $bills->perPage() }}</td>
                    <td><a href="{{ route('bills.show', $bill) }}">{{ $bill->number }}</a></td>
                    <td>{{ $bill->customer->name ?? $bill->customer_name ?? '-' }}</td>
                    <td>{{ optional($bill->date)->format('Y-m-d') ?? $bill->created_at->format('Y-m-d') }}</td>
                    <td>{{ number_format($bill->amount, 2) }}</td>
                    <td>{{ ucfirst($bill->status ?? 'n/a') }}</td>
                    <td class="text-end">
                        <a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('bills.edit', $bill) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this bill?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div>
            Showing {{ $bills->firstItem() }} - {{ $bills->lastItem() }} of {{ $bills->total() }} bills
        </div>
        <div>
            {{ $bills->withQueryString()->links() }}
        </div>
    </div>
    @else
        <div class="alert alert-info">No bills found.</div>
    @endif
</div>

@endsection
