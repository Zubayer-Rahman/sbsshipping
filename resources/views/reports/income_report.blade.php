@extends('layouts.app')
@section('content')
    <div style="padding: 2rem;">
        <h1>Income Report</h1>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 20px;">
            <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 5px solid var(--success);">
                <label>Total Income</label>
                <h2>TK. {{ number_format($totalIncome, 2) }}</h2>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 5px solid var(--danger);">
                <label>Total Expense</label>
                <h2>TK. {{ number_format($totalExpense, 2) }}</h2>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 5px solid var(--primary);">
                <label>Net Profit</label>
                <h2>TK. {{ number_format($netProfit, 2) }}</h2>
            </div>
        </div>
    </div>
@endsection