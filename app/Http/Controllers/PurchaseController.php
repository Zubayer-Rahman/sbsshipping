<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // ── List ─────────────────────────────────────────────────────────────────
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
        if ($request->filled('status'))        $query->where('purchase_status', $request->status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('date_from'))     $query->whereDate('purchase_date', '>=', $request->date_from);
        if ($request->filled('date_to'))       $query->whereDate('purchase_date', '<=', $request->date_to);

        $purchases = $query->paginate($request->input('per_page', 50));

        return view('expenses.PurchaseList', compact('purchases'));
    }

    // ── Create form ───────────────────────────────────────────────────────────
    public function create()
    {
        $suppliers = Contact::where('type', 'supplier')
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        $items = Item::orderBy('item_name')->get();
        $jobs  = DB::table('sbs_jobs')->orderByDesc('id')->get(['id', 'job_no', 'job_id', 'client_name']);

        return view('expenses.PurchaseCreate', compact('suppliers', 'items', 'jobs'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required',
            'purchase_date' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            // Build purchase
            $supplier = Contact::find($request->supplier_id);

            // File upload
            $docPath = null;
            if ($request->hasFile('document')) {
                $docPath = $request->file('document')->store('purchase_docs', 'public');
            }

            // Recalculate totals from items
            $itemNames  = $request->input('item_name', []);
            $quantities = $request->input('purchase_quantity', []);
            $unitCosts  = $request->input('unit_cost', []);
            $discounts  = $request->input('discount_percent', []);
            $margins    = $request->input('profit_margin', []);
            $sellPrices = $request->input('unit_selling_price', []);

            $netTotal   = 0;
            $totalItems = 0;

            $lineItems = [];
            foreach ($itemNames as $i => $name) {
                if (empty($name)) continue;

                $qty     = floatval($quantities[$i] ?? 1);
                $cost    = floatval($unitCosts[$i] ?? 0);
                $disc    = floatval($discounts[$i] ?? 0);
                $costAfterDisc = $cost * (1 - $disc / 100);
                $lineTotal = $costAfterDisc * $qty;

                $netTotal   += $lineTotal;
                $totalItems += $qty;

                $lineItems[] = [
                    'item_name'          => $name,
                    'item_code'          => $request->input("item_code.$i"),
                    'purchase_quantity'  => $qty,
                    'unit'               => $request->input("unit.$i"),
                    'unit_cost'          => $cost,
                    'discount_percent'   => $disc,
                    'unit_cost_before_tax' => $costAfterDisc,
                    'line_total'         => $lineTotal,
                    'profit_margin'      => floatval($margins[$i] ?? 0),
                    'unit_selling_price' => floatval($sellPrices[$i] ?? 0),
                ];
            }

            $paymentAmount = floatval($request->input('payment_amount', 0));
            $paymentStatus = $paymentAmount >= $netTotal ? 'Paid'
                : ($paymentAmount > 0 ? 'Partial' : 'Due');

            $purchase = Purchase::create([
                'supplier_id'      => $request->supplier_id,
                'supplier_name'    => $supplier?->business_name,
                'supplier_address' => $supplier?->address,
                'business_location' => 'SBS Shipping (BL0001)',
                'purchase_date'    => $request->purchase_date,
                'pay_term_number'  => $request->pay_term_number,
                'pay_term_type'    => $request->pay_term_type,
                'document_path'    => $docPath,
                'purchase_status'  => 'Received',
                'total_items'      => $totalItems,
                'net_total'        => $netTotal,
                'grand_total'      => $netTotal,
                'payment_amount'   => $paymentAmount,
                'payment_status'   => $paymentStatus,
                'paid_on'          => $request->paid_on ?: now(),
                'payment_method'   => $request->payment_method ?? 'Cash',
                'payment_account'  => $request->payment_account,
                'payment_note'     => $request->payment_note,
                'user_id'          => Auth::id(),
                'added_by'         => Auth::user()->name,
            ]);

            foreach ($lineItems as $line) {
                $purchase->items()->create($line);
            }
        });

        return redirect()->route('purchases.list')
            ->with('success', 'Purchase added successfully!');
    }

    // ── Show (modal data) ────────────────────────────────────────────────────
    public function show(Purchase $purchase)
    {
        $purchase->load('items');
        return view('expenses.PurchaseShow', compact('purchase'));
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return back()->with('success', 'Purchase deleted.');
    }

    // ── AJAX: item search ─────────────────────────────────────────────────────
    public function searchItems(Request $request)
    {
        $items = Item::where('item_name', 'like', '%' . $request->q . '%')
            ->orWhere('item_code', 'like', '%' . $request->q . '%')
            ->limit(10)
            ->get(['id', 'item_name', 'item_code', 'unit', 'exc_tax', 'billing_exc_tax']);

        return response()->json($items);
    }
}
