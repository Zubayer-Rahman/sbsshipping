<?php

namespace App\Http\Controllers;

use App\Models\PaymentAccount;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentAccountController extends Controller
{
    // List all accounts
    public function index()
    {
        $accounts = PaymentAccount::with('creator')->latest()->get();

        $totalBalance = $accounts->sum('current_balance');
        $activeAccounts = $accounts->where('is_active', true)->count();


        $depositRoutes = $accounts->mapWithKeys(function ($account) {
            return [$account->id => route('accounts.deposit', $account)];
        })->toArray();

        return view('accounts.index', compact('accounts', 'totalBalance', 'activeAccounts', 'depositRoutes'));
    }

    // Show create form
    public function create()
    {
        return view('accounts.create');
    }

    // Store new account
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            // 'account_type' => 'required|in:bank,cash,mobile_banking,other',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['account_type'] = 'other'; // Default to 'other' for now since we're hiding the option
        $validated['current_balance'] = 0;
        $validated['created_by'] = Auth::id();

        $account = PaymentAccount::create($validated);

        // Record the opening balance as the first transaction if > 0
        if ($validated['opening_balance'] > 0) {
            $account->recordTransaction(
                'credit',
                $validated['opening_balance'],
                'manual',
                $account->id,
                'Opening Balance',
                now(),
                Auth::id()
            );
        }

        return redirect()->route('accounts.index')->with('success', 'Account created successfully!');
    }

    // Show single account with transaction history
    public function show(PaymentAccount $account)
    {
        $account->load(['transactions' => function ($query) {
            $query->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc'); // tiebreaker for same-day transactions
        }]);

        return view('accounts.show', compact('account'));
    }

    public function destroy(PaymentAccount $account)
    {

        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully!');
    }

    // Cash flow page - shows transactions across all accounts
    public function cashFlow(Request $request)
    {
        $query = AccountTransaction::with(['account', 'creator']);

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        // Filter by account
        if ($request->has('account_id') && $request->account_id) {
            $query->where('payment_account_id', $request->account_id);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(30);

        $accounts = PaymentAccount::where('is_active', true)->get();

        // Calculate totals
        $totalCredit = $query->where('transaction_type', 'credit')->sum('amount');
        $totalDebit = $query->where('transaction_type', 'debit')->sum('amount');

        return view('accounts.cashflow', compact('transactions', 'accounts', 'totalCredit', 'totalDebit'));
    }

    public function adjustBalance(Request $request, PaymentAccount $account)
    {
        $validated = $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $account->recordTransaction(
            $validated['type'],
            $validated['amount'],
            'manual',
            $account->id,
            $validated['description'],
            $validated['date'],
            Auth::id()
        );

        return redirect()->back()->with('success', 'Account balance adjusted successfully!');
    }

    public function deposit(Request $request, PaymentAccount $account)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date'        => 'required|date',
        ]);

        $account->recordTransaction(
            'credit',
            $validated['amount'],
            'deposit',
            $account->id,
            $validated['description'],
            $validated['date'],
            Auth::id()
        );

        return redirect()->route('accounts.index')
            ->with('success', '৳' . number_format($validated['amount'], 2) . ' deposited to ' . $account->account_name . ' successfully!');
    }

    // Transfer money between accounts
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:payment_accounts,id',
            'to_account_id'   => 'required|exists:payment_accounts,id|different:from_account_id',
            'amount'          => 'required|numeric|min:0.01',
            'description'     => 'nullable|string|max:255',
            'date'            => 'required|date',
        ]);

        $fromAccount = PaymentAccount::findOrFail($validated['from_account_id']);
        $toAccount   = PaymentAccount::findOrFail($validated['to_account_id']);

        // ✅ Balance check removed — accounts can go negative

        $description = $validated['description']
            ?: 'Transfer from ' . $fromAccount->account_name . ' to ' . $toAccount->account_name;

        DB::transaction(function () use ($fromAccount, $toAccount, $validated, $description) {
            $fromAccount->recordTransaction(
                'debit',
                $validated['amount'],
                'transfer',
                $toAccount->id,
                'Transfer to ' . $toAccount->account_name . ': ' . $description,
                $validated['date'],
                Auth::id()
            );

            $toAccount->recordTransaction(
                'credit',
                $validated['amount'],
                'transfer',
                $fromAccount->id,
                'Transfer from ' . $fromAccount->account_name . ': ' . $description,
                $validated['date'],
                Auth::id()
            );
        });

        return redirect()->route('accounts.index')
            ->with('success', '৳' . number_format($validated['amount'], 2) . ' transferred from ' . $fromAccount->account_name . ' to ' . $toAccount->account_name . ' successfully!');
    }

    public static function refundTransactions(string $sourceType, int $sourceId, string $description = null): void
    {
        // Find all transactions linked to this source
        $transactions = AccountTransaction::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        foreach ($transactions as $transaction) {
            $account = PaymentAccount::find($transaction->payment_account_id);

            if (!$account) {
                continue;
            }

            // Reverse the transaction type
            $refundType = $transaction->transaction_type === 'credit' ? 'debit' : 'credit';

            $account->recordTransaction(
                $refundType,
                $transaction->amount,
                'refund',
                $sourceId,
                $description ?? 'Refund: ' . ucfirst($sourceType) . ' #' . $sourceId . ' deleted',
                now(),
                Auth::id() ?? $transaction->created_by
            );

            // Delete the original transaction
            $transaction->delete();
        }
    }

    private function generateReference($sourceType, $sourceId)
    {
        return strtoupper(substr($sourceType, 0, 3)) . '-' . str_pad($sourceId, 6, '0', STR_PAD_LEFT);
    }
    // Toggle active status
    public function toggleActive(PaymentAccount $account)
    {
        $account->is_active = !$account->is_active;
        $account->save();

        return redirect()->back()->with('success', 'Account status updated!');
    }
}
