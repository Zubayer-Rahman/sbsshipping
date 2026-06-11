@extends('layouts.app')
@section('content')
    <div style="padding: 2rem;">
        <h1>Expense Report</h1>
        <p>Total Expenses: TK. {{ number_format($totalExpense, 2) }}</p>
        {{-- Add your table here later --}}
    </div>
@endsection