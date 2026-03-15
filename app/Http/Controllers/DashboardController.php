<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Top 4 summary boxes ───────────────────────────────────────────
        $totalBill    = Job::sum('cost_amount') ?? 0;
        $totalExpense = Job::sum('expense_amount') ?? 0;
        $totalDues    = $totalBill - $totalExpense;
        $totalJobs    = Job::count();

        // ── Monthly jobs count (last 12 months) – Line / Bar chart ────────
        $monthlyJobs = Job::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // ── Monthly revenue (last 12 months) – Bar chart ──────────────────
        $monthlyRevenue = Job::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(cost_amount) as total')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // ── Job status distribution – Pie chart ───────────────────────────
        $statusData = Job::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // ── Cargo type distribution – Pie chart ───────────────────────────
        $cargoData = Job::select('cargo_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('cargo_type')
            ->groupBy('cargo_type')
            ->get();

        // ── Recent 5 jobs ─────────────────────────────────────────────────
        $recentJobs = Job::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalBill', 'totalExpense', 'totalDues', 'totalJobs',
            'monthlyJobs', 'monthlyRevenue', 'statusData', 'cargoData',
            'recentJobs'
        ));
    }
}