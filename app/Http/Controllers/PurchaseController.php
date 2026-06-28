<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PaymentAccount;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PurchaseController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Purchase::with('items')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference_no', 'like', "%$s%")
                    ->orWhere('supplier_name', 'like', "%$s%");
            });
        }
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('date_from'))      $query->whereDate('purchase_date', '>=', $request->date_from);
        if ($request->filled('date_to'))        $query->whereDate('purchase_date', '<=', $request->date_to);

        $purchases = $query->paginate($request->input('per_page', 50));

        // Support both view locations
        $view = View::exists('purchases.list') ? 'purchases.list' : 'expenses.PurchaseList';
        return view($view, compact('purchases'));
    }

    // ── Create form ────────────────────────────────────────────────────────────
    public function create()
    {
        $suppliers = Contact::where('type', 'supplier')
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        $items = Item::orderBy('item_name')->get();
        $jobs = \App\Models\Job::select('id', 'job_no', 'job_id', 'client_name', 'category', 'type', 'invoice_value_usd')
            ->orderBy('id', 'desc')
            ->get();

        $view = View::exists('purchases.create') ? 'purchases.create' : 'expenses.PurchaseCreate';
        return view($view, compact('suppliers', 'items', 'jobs'));
    }

    // ── Store ──────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required',
            'purchase_date' => 'required',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_account_id' => 'required|exists:payment_accounts,id',
        ]);

        $account = PaymentAccount::find($request->payment_account_id);

        // // 1. Check Balance
        // if ($request->payment_amount > $account->current_balance) {
        //     return redirect()->back()->with('error', 'Insufficient funds in ' . $account->account_name)->withInput();
        // }

        DB::transaction(function () use ($request, $account) {
            $supplier = Contact::find($request->supplier_id);

            // File upload
            $docPath = null;
            if ($request->hasFile('document')) {
                $docPath = $request->file('document')->store('purchase_docs', 'public');
            }

            // Calculate totals from line items
            $itemNames  = $request->input('item_name', []);
            $quantities = $request->input('purchase_quantity', []);
            $unitCosts  = $request->input('unit_cost', []);
            $discounts  = $request->input('discount_percent', []);
            $margins    = $request->input('profit_margin', []);
            $sellPrices = $request->input('unit_selling_price', []);

            $netTotal   = 0;
            $totalItems = 0;
            $lineItems  = [];

            foreach ($itemNames as $i => $name) {
                if (empty(trim($name))) continue;

                $qty           = floatval($quantities[$i] ?? 1);
                $cost          = floatval($unitCosts[$i] ?? 0);
                $disc          = floatval($discounts[$i] ?? 0);
                $costAfterDisc = $cost * (1 - $disc / 100);
                $lineTotal     = $costAfterDisc * $qty;

                $netTotal   += $lineTotal;
                $totalItems += $qty;

                $lineItems[] = [
                    'item_name'            => $name,
                    'item_code'            => $request->input("item_code.$i"),
                    'purchase_quantity'    => $qty,
                    'unit'                 => $request->input("unit.$i"),
                    'unit_cost'            => $cost,
                    'discount_percent'     => $disc,
                    'unit_cost_before_tax' => $costAfterDisc,
                    'line_total'           => $lineTotal,
                    'profit_margin'        => floatval($margins[$i] ?? 0),
                    'unit_selling_price'   => floatval($sellPrices[$i] ?? 0),
                ];
            }

            $paymentAmount = floatval($request->input('payment_amount', 0));
            $paymentStatus = ($paymentAmount >= $netTotal && $netTotal > 0)
                ? 'Paid'
                : ($paymentAmount > 0 ? 'Partial' : 'Due');

            // Job selections from multi-select
            $jobIds   = array_filter((array) $request->input('job_ids', []));
            $jobRefNo = $request->input('job_ref_no');

            $purchase = Purchase::create([
                'supplier_id'       => $request->supplier_id,
                'supplier_name'     => $supplier?->business_name,
                'supplier_address'  => $supplier?->address,
                'business_location' => 'SBS Shipping (BL0001)',
                'purchase_date'     => $request->purchase_date,
                'document_path'     => $docPath,
                'purchase_status'   => 'Received',
                'total_items'       => $totalItems,
                'net_total'         => $netTotal,
                'grand_total'       => $netTotal,
                'payment_amount'    => $paymentAmount,
                'payment_status'    => $paymentStatus,
                'paid_on'           => $request->paid_on ?: now(),
                'payment_method'    => $request->payment_method ?? 'Cash',
                'payment_account_id' => $request->payment_account_id,
                'payment_note'      => $request->payment_note,
                'job_ref_no'        => $jobRefNo,
                'user_id'           => Auth::id(),
                'added_by'          => Auth::user()->name,
            ]);

            // 3. REAL-TIME DEDUCTION (The line that was missing or failing)
            $account->recordTransaction(
                'debit',
                $request->payment_amount,
                'purchase',
                $purchase->id,
                "Purchase Payment: " . $purchase->reference_no,
                now(),
                Auth::id()
            );

            foreach ($lineItems as $line) {
                $purchase->items()->create($line);
            }
        });

        return redirect()->route('purchases.list')
            ->with('success', 'Purchase added successfully!');
    }

    // ── Show ───────────────────────────────────────────────────────────────────
    public function show(Purchase $purchase)
    {
        $purchase->load('items');
        $view = View::exists('purchases.show') ? 'purchases.show' : 'expenses.PurchaseShow';
        return view($view, compact('purchase'));
    }

    // ── Delete ─────────────────────────────────────────────────────────────────
    public function destroy(Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($purchase) {

                $transactions = AccountTransaction::where('source_type', 'purchase')
                    ->where('source_id', $purchase->id)
                    ->get();

                foreach ($transactions as $transaction) {
                    $account = PaymentAccount::find($transaction->payment_account_id);

                    if ($account) {
                        $refundType = $transaction->transaction_type === 'credit' ? 'debit' : 'credit';

                        $account->recordTransaction(
                            $refundType,
                            $transaction->amount,
                            'refund',
                            $purchase->id,
                            'Refund: Purchase #' . $purchase->id . ' deleted',
                            now(),
                            Auth::id()
                        );
                    }

                    $transaction->delete();
                }

                $purchase->delete();
            });

            return back()->with('success', 'Purchase deleted and amount refunded to account.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting purchase: ' . $e->getMessage());
        }
    }

    // ── AJAX item search ───────────────────────────────────────────────────────
    public function searchItems(Request $request)
    {
        $items = Item::where('item_name', 'like', '%' . $request->q . '%')
            ->orWhere('item_code', 'like', '%' . $request->q . '%')
            ->limit(10)
            ->get(['id', 'item_name', 'item_code', 'unit', 'exc_tax', 'billing_exc_tax']);

        return response()->json($items);
    }
}
