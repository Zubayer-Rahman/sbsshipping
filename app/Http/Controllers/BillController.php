<?php

namespace App\Http\Controllers;

use App\Helpers\JobChargeCalculator;

use App\Models\Bill;
use App\Models\Job;
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

        $bill = DB::transaction(function () use ($request) {
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

            // Additional Expenses Total (from hidden input)
            $additionalExpensesTotal = floatval($request->additional_expenses_total ?? 0);


            $discType   = 'None';
            $discAmt    = 0;
            $discValue  = 0;
            $taxValue   = 0;

            $shipping      = floatval($request->shipping_charges ?? 0);
            $totalPayable  = $subTotal + $additionalExpensesTotal + $shipping;
            $paymentAmount = floatval($request->payment_amount ?? 0);
            $totalRemaining = $totalPayable - $paymentAmount;
            $payStatus      = $totalRemaining <= 0 ? 'Paid' : ($paymentAmount > 0 ? 'Partial' : 'Due');


            // Generate a unique bill_no by finding the highest one and incrementing
            $lastBill = Bill::orderByDesc('id')->first();
            $nextBillNo = $lastBill ? (intval($lastBill->bill_no) + 1) : 1;

            // Make sure it doesn't already exist (safety check)
            while (Bill::where('bill_no', $nextBillNo)->exists()) {
                $nextBillNo++;
            }

            $bill = Bill::create([
                'bill_no'           => $nextBillNo,
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

            // ── Save Additional Expenses ──
            if ($request->has('additional_expenses') && is_array($request->additional_expenses)) {
                foreach ($request->additional_expenses as $expenseData) {
                    if (!empty($expenseData['amount']) && floatval($expenseData['amount']) > 0) {
                        \App\Models\BillAdditionalExpense::create([
                            'bill_id'     => $bill->id,
                            'description' => $expenseData['description'] ?? 'Additional Expense',
                            'amount'      => floatval($expenseData['amount']),
                            'job_id'      => $expenseData['job_id'] ?? null,
                            'is_auto'     => !empty($expenseData['is_auto']),
                        ]);
                    }
                }
            }

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
            return $bill;
        });

        if ($request->input('action') === 'save_print') {
            $bill = Bill::latest()->first();
            return redirect()->route('bills.print', $bill->id);
        }


        if ($request->has('additional_expenses') && is_array($request->additional_expenses)) {
            foreach ($request->additional_expenses as $expenseData) {
                if (!empty($expenseData['amount']) && floatval($expenseData['amount']) > 0) {
                    \App\Models\BillAdditionalExpense::create([
                        'bill_id'     => $bill->id,
                        'description' => $expenseData['description'] ?? 'Additional Expense',
                        'amount'      => floatval($expenseData['amount']),
                        'job_id'      => $expenseData['job_id'] ?? null,
                        'is_auto'     => !empty($expenseData['is_auto']),
                    ]);

                    // ★★★ Mark the original AdditionalExpense as "billed" ★★★
                    if (!empty($expenseData['additional_expense_id'])) {
                        \App\Models\AdditionalExpense::where('id', $expenseData['additional_expense_id'])
                            ->update([
                                'status' => 'billed',
                                'billed_to_bill_id' => $bill->id,
                                'billed_at' => now(),
                            ]);
                    }
                }
            }
        }

        return redirect()->route('bills.list')->with('success', 'Bill created successfully!');
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(Bill $bill)
    {
        $bill->load(['items', 'payments', 'additionalExpenses.job']);
        return view('bills.show', compact('bill'));
    }

    public function print($id)
    {
        $bill = Bill::with(['items', 'additionalExpenses'])->findOrFail($id);

        // Fetch related job using job_number
        $job = null;
        if ($bill->job_number) {
            $job = \App\Models\Job::where('id', $bill->job_number)
                ->orWhere('job_id', $bill->job_number)
                ->orWhere('job_no', $bill->job_number)
                ->first();
        }

        return view('bills.print', compact('bill', 'job'));
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

    /**
     * AJAX endpoint to fetch job details with calculated service charge
     */
    public function getJobDetails($jobId)
    {
        $job = Job::find($jobId);

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        $value = (float) ($job->imp_exp_value ?? $job->invoice_value_usd ?? 0);
        $category = strtolower(trim($job->category ?? ''));
        $type = strtoupper(trim($job->type ?? ''));
        $qty = (float) ($job->quantity ?? 0);
        $clientName = strtolower(trim($job->client_name ?? ''));

        $percentage = 0;

        // Category checks
        $isImportAir = str_contains($category, 'import') && str_contains($category, 'air');
        $isImportSea = str_contains($category, 'import') && str_contains($category, 'sea');
        $isImport = str_contains($category, 'import') || str_contains($category, 'imp');
        $isExport = str_contains($category, 'export') || str_contains($category, 'exp');
        $isByTruck = str_contains($category, 'truck');

        // Client check
        $isHitechImport = str_contains($clientName, 'hitech') && $isImport;

        // Skip agency commission for import by air
        if ($isImportAir) {
            return response()->json([
                'success' => true,
                'job' => [
                    'id' => $job->id,
                    'job_id' => $job->job_id,
                    'job_no' => $job->job_no,
                    'client_name' => $job->client_name,
                    'category' => $job->category,
                    'type' => $type,
                    'quantity' => $qty,
                ],
                'calculation' => [
                    'percentage' => 0,
                    'service_charge_amount' => 0,
                    'imp_exp_value' => $value,
                    'skip_agency_commission' => true,
                ]
            ]);
        }

        // Calculate percentage-based commission
        if ($isImport) {
            if ($value <= 20000) {
                $percentage = 0.13;
            } elseif ($value <= 50000) {
                $percentage = 0.12;
            } elseif ($value <= 100000) {
                $percentage = 0.09;
            } else {
                $percentage = 0.07;
            }
        } elseif ($isExport) {
            if ($value <= 50000) {
                $percentage = 0.11;
            } elseif ($value <= 100000) {
                $percentage = 0.08;
            } else {
                $percentage = 0.06;
            }
        }

        // Calculate base service charge (percentage of value)
        $calculatedCharge = round($value * $percentage, 2);

        // Determine minimum commission based on job type
        $minimumCommission = 0;
        $commissionType = '';

        if ($isHitechImport) {
            $minimumCommission = 850;
            $commissionType = 'Hitech Import Minimum';
        } elseif ($isImportSea) {
            $minimumCommission = 1150;
            $commissionType = 'Import by Sea Minimum';
        } elseif ($isExport || $isByTruck) {
            $minimumCommission = 750;
            $commissionType = ($isByTruck ? 'Truck' : 'Export') . ' Minimum';
        }

        // Use the higher of calculated vs minimum
        $finalCharge = max($calculatedCharge, $minimumCommission);
        $isMinimumApplied = $finalCharge === $minimumCommission && $minimumCommission > 0;

        return response()->json([
            'success' => true,
            'job' => [
                'id' => $job->id,
                'job_id' => $job->job_id,
                'job_no' => $job->job_no,
                'client_name' => $job->client_name,
                'category' => $job->category,
                'type' => $type,
                'quantity' => $qty,
            ],
            'calculation' => [
                'percentage' => $percentage,
                'calculated_charge' => $calculatedCharge,
                'minimum_commission' => $minimumCommission,
                'service_charge_amount' => $finalCharge,
                'imp_exp_value' => $value,
                'skip_agency_commission' => false,
                'is_minimum_applied' => $isMinimumApplied,
                'commission_type' => $isMinimumApplied ? $commissionType : 'Percentage Based',
            ]
        ]);
    }
}
