<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\BillPayment;
use App\Models\Contact;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Bill::with(['items', 'payments'])->latest('billing_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bill_no', 'like', "%$s%")
                    ->orWhere('client_name', 'like', "%$s%")
                    ->orWhere('job_number', 'like', "%$s%");
            });
        }
        if ($request->filled('client'))          $query->where('client_id', $request->client);
        if ($request->filled('payment_status'))  $query->where('payment_status', $request->payment_status);
        if ($request->filled('payment_method'))  $query->where('payment_method', $request->payment_method);
        if ($request->filled('shipping_status')) $query->where('shipping_status', $request->shipping_status);
        if ($request->filled('date_from'))       $query->whereDate('billing_date', '>=', $request->date_from);
        if ($request->filled('date_to'))         $query->whereDate('billing_date', '<=', $request->date_to);

        $bills   = $query->paginate($request->input('per_page', 50));
        $clients = Contact::where('type', 'client')->where('is_active', true)->orderBy('business_name')->get();

        return view('bills.list', compact('bills', 'clients'));
    }

    // ── Create form ───────────────────────────────────────────────────────────
    public function create()
    {
        $clients = Contact::where('type', 'client')->where('is_active', true)->orderBy('business_name')->get();
        $items   = Item::orderBy('item_name')->get();
        return view('bills.create', compact('clients', 'items'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'client_id'    => 'required',
            'billing_date' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $client = Contact::find($request->client_id);

            // Line items
            $names       = $request->input('item_name', []);
            $descs       = $request->input('description', []);
            $qtys        = $request->input('quantity', []);
            $prices      = $request->input('unit_price', []);
            $discounts   = $request->input('item_discount', []);
            $taxes       = $request->input('item_tax', []);

            $subTotal  = 0;
            $totalItems = 0;
            $lineItems = [];

            foreach ($names as $i => $name) {
                if (empty(trim($name))) continue;
                $qty       = floatval($qtys[$i] ?? 1);
                $price     = floatval($prices[$i] ?? 0);
                $disc      = floatval($discounts[$i] ?? 0);
                $tax       = floatval($taxes[$i] ?? 0);
                $priceIncTax = $price * (1 + $tax / 100);
                $subtotal    = ($price - $disc) * $qty;
                $subTotal   += $subtotal;
                $totalItems += $qty;
                $lineItems[] = compact('name', 'i') + [
                    'item_name'    => $name,
                    'item_code'    => $request->input("item_code.$i"),
                    'description'  => $descs[$i] ?? null,
                    'quantity'     => $qty,
                    'unit'         => $request->input("unit.$i"),
                    'unit_price'   => $price,
                    'discount'     => $disc,
                    'tax'          => $tax,
                    'price_inc_tax' => $priceIncTax,
                    'subtotal'     => $subtotal,
                ];
            }

            // Discount
            $discType   = $request->discount_type ?? 'Percentage';
            $discAmt    = floatval($request->discount_amount ?? 0);
            $discValue  = $discType === 'Percentage' ? ($subTotal * $discAmt / 100) : $discAmt;

            // Order tax
            $taxRate   = floatval($request->order_tax_rate ?? 0);
            $taxValue  = ($subTotal - $discValue) * $taxRate / 100;

            $shipping      = floatval($request->shipping_charges ?? 0);
            $totalPayable  = $subTotal - $discValue + $taxValue + $shipping;
            $paymentAmount = floatval($request->payment_amount ?? 0);
            $totalRemaining = $totalPayable - $paymentAmount;
            $payStatus      = $totalRemaining <= 0 ? 'Paid' : ($paymentAmount > 0 ? 'Partial' : 'Due');

            $bill = Bill::create([
                'business_location' => 'SBS Shipping (BL0001)',
                'client_id'         => $request->client_id,
                'client_name'       => $client?->business_name,
                'client_contact'    => $client?->mobile,
                'billing_address'   => $client?->address,
                'shipping_address'  => $client?->address,
                'pay_term_number'   => $request->pay_term_number,
                'pay_term_type'     => $request->pay_term_type,
                'billing_date'      => $request->billing_date,
                'status'            => $request->status ?? 'Final',
                'job_number'        => $request->job_number,
                'shipping_status'   => $request->shipping_status,
                'discount_type'     => $discType,
                'discount_amount'   => $discAmt,
                'discount_value'    => $discValue,
                'order_tax'         => $request->order_tax ?? 'None',
                'order_tax_value'   => $taxValue,
                'total_items'       => $totalItems,
                'sub_total'         => $subTotal,
                'shipping_charges'  => $shipping,
                'total_payable'     => $totalPayable,
                'total_paid'        => $paymentAmount,
                'total_remaining'   => max(0, $totalRemaining),
                'payment_status'    => $payStatus,
                'payment_method'    => $request->payment_method,
                'payment_account'   => $request->payment_account,
                'payment_note'      => $request->payment_note,
                'paid_on'           => $request->paid_on ?: now(),
                'billing_note'      => $request->billing_note,
                'staff_note'        => $request->staff_note,
                'user_id'           => Auth::id(),
                'added_by'          => Auth::user()->name,
            ]);

            foreach ($lineItems as $line) {
                $bill->items()->create($line);
            }

            if ($paymentAmount > 0) {
                $bill->payments()->create([
                    'reference_no'   => 'PP' . date('Y') . '/' . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
                    'amount'         => $paymentAmount,
                    'payment_method' => $request->payment_method ?? 'Cash',
                    'payment_note'   => $request->payment_note,
                    'paid_on'        => $request->paid_on ?: now(),
                    'user_id'        => Auth::id(),
                ]);
            }
        });

        if ($request->input('action') === 'save_print') {
            $bill = Bill::latest()->first();
            return redirect()->route('bills.print', $bill->id);
        }

        return redirect()->route('bills.list')->with('success', 'Bill created successfully!');
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(Bill $bill)
    {
        $bill->load(['items', 'payments']);
        return view('bills.show', compact('bill'));
    }

    // ── Print invoice ─────────────────────────────────────────────────────────
    public function printInvoice(Bill $bill)
    {
        $bill->load(['items', 'payments']);
        return view('bills.print', compact('bill'));
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function destroy(Bill $bill)
    {
        $bill->delete();
        return back()->with('success', 'Bill deleted.');
    }

    // ── Add payment ───────────────────────────────────────────────────────────
    public function addPayment(Request $request, Bill $bill)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);

        $bill->payments()->create([
            'reference_no'   => 'PP' . date('Y') . '/' . str_pad(BillPayment::count() + 1, 4, '0', STR_PAD_LEFT),
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method ?? 'Cash',
            'payment_note'   => $request->payment_note,
            'paid_on'        => $request->paid_on ?: now(),
            'user_id'        => Auth::id(),
        ]);

        $totalPaid      = $bill->payments()->sum('amount');
        $totalRemaining = $bill->total_payable - $totalPaid;
        $bill->update([
            'total_paid'      => $totalPaid,
            'total_remaining' => max(0, $totalRemaining),
            'payment_status'  => $totalRemaining <= 0 ? 'Paid' : ($totalPaid > 0 ? 'Partial' : 'Due'),
            'payment_method'  => $request->payment_method ?? $bill->payment_method,
        ]);

        return back()->with('success', 'Payment added.');
    }

    // ── AJAX: client info ─────────────────────────────────────────────────────
    public function clientInfo(Request $request)
    {
        $client = Contact::find($request->id);
        return response()->json($client);
    }

    // ── AJAX: item search ─────────────────────────────────────────────────────
    public function searchItems(Request $request)
    {
        $items = Item::where('item_name', 'like', '%' . $request->q . '%')
            ->orWhere('item_code', 'like', '%' . $request->q . '%')
            ->limit(10)
            ->get(['id', 'item_name', 'item_code', 'unit', 'billing_exc_tax']);
        return response()->json($items);
    }
}
