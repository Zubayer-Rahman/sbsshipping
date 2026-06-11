<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Bill;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Iou;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function clientReport(Request $request)
    {
        $contacts = Contact::where('type', 'client')
            ->withSum('bills as total_billed', 'total_payable')
            ->withSum('bills as total_paid', 'total_paid')
            ->latest()
            ->paginate(50);

        return view('reports.contact_report', [
            'contacts' => $contacts,
            'title' => 'Client Report',
            'type' => 'client'
        ]);
    }

    public function supplierReport(Request $request)
    {
        $contacts = Contact::where('type', 'supplier')
            ->withSum('purchases as total_purchased', 'payment_amount') // Adjust column name if needed
            ->latest()
            ->paginate(50);

        return view('reports.contact_report', [
            'contacts' => $contacts,
            'title' => 'Supplier Report',
            'type' => 'supplier'
        ]);
    }

    public function contactLedger(Contact $contact)
    {
        // Fetch all transaction types for this contact
        $bills = Bill::where('client_id', $contact->id)->get()->map(fn($i) => [...$i->toArray(), 't_type' => 'Bill']);
        $purchases = Purchase::where('supplier_id', $contact->id)->get()->map(fn($i) => [...$i->toArray(), 't_type' => 'Purchase']);
        $ious = Iou::where('contact_id', $contact->id)->get()->map(fn($i) => [...$i->toArray(), 't_type' => 'IOU']);

        // Merge and sort by date
        $transactions = $bills->concat($purchases)->concat($ious)->sortByDesc('created_at');

        return view('reports.ledger', compact('contact', 'transactions'));
    }

    public function expenseReport(Request $request)
    {
        // Fetch all main expenses grouped by category
        $expenses = Expense::with(['category'])
            ->latest('expense_date')
            ->paginate(50);

        $totalExpense = Expense::sum('total_amount');

        return view('reports.expense_report', compact('expenses', 'totalExpense'));
    }

    public function incomeReport(Request $request)
    {
        // Simple Income vs Expense calculation
        $totalIncome = Bill::sum('total_paid');
        $totalExpense = Expense::sum('total_amount');
        $netProfit = $totalIncome - $totalExpense;

        return view('reports.income_report', compact('totalIncome', 'totalExpense', 'netProfit'));
    }
}
