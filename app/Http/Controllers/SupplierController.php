<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Supplier;
use App\Models\SupplierGRNMaster;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class SupplierController extends Controller
{

    public function index()
    {
        $suppliers = Supplier::all();
        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('supplier.create');
    }

public function store(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|string|unique:supplier,supplier_id',
        'supplier_name' => 'required|string|unique:supplier,supplier_name',
        'phone' => 'required|digits:10',
        'address' => 'required|string',
    ], [
        'supplier_id.required' => 'Supplier ID is required.',
        'supplier_id.unique' => 'This Supplier ID already exists.',
        'supplier_name.required' => 'Supplier name is required.',
        'supplier_name.unique' => 'This Supplier name is already taken.',
        'phone.required' => 'Phone number is required.',
        'phone.digits' => 'Phone number must be exactly 10 digits.',
        'address.required' => 'Address is required.',
    ]);

    Supplier::create($request->all());

    return redirect()->route('supplier.index')->with('success', 'Supplier added successfully.');
}

public function update(Request $request, Supplier $supplier)
{
    $request->validate([
        'supplier_id' => 'required|string|unique:supplier,supplier_id,' . $supplier->id,
        'supplier_name' => 'required|string|unique:supplier,supplier_name,' . $supplier->id,
        'phone' => 'required|digits:10',
        'address' => 'required|string',
    ], [
        'supplier_id.required' => 'Supplier ID is required.',
        'supplier_id.unique' => 'This Supplier ID already exists.',
        'supplier_name.required' => 'Supplier name is required.',
        'supplier_name.unique' => 'This Supplier name is already taken.',
        'phone.required' => 'Phone number is required.',
        'phone.digits' => 'Phone number must be exactly 10 digits.',
        'address.required' => 'Address is required.',
    ]);

    $supplier->update($request->all());

    return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
}

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully.');
    }

public function supplierLedger(Request $request)
{
    $supplierId = $request->input('supplier_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if (!$supplierId) {
        return view('ledger.supplier_ledger_form');
    }

    $supplier = Supplier::find($supplierId);
    if (!$supplier) {
        return back()->withErrors(['supplier_id' => 'Invalid supplier ID'])->withInput();
    }

    if ($startDate && $endDate && $startDate > $endDate) {
        return back()->withErrors(['end_date' => 'End date must be after start date'])->withInput();
    }

    $supplierName = $supplier->supplier_name;

    // Calculate Opening Balance (all unpaid balance before startDate)
    $openingBalanceQuery = SupplierGRNMaster::where('supplier_name', $supplierName);
    if ($startDate) {
        $openingBalanceQuery->where('g_date', '<', $startDate);
    }

    $openingBalance = 0;
    foreach ($openingBalanceQuery->get() as $transaction) {
        $totalBill = $transaction->tobe_price ?? 0;
        $paidAmount = $transaction->supplier_pay ?? 0;
        $remainingFromBill = $totalBill - $paidAmount;
        $openingBalance += $remainingFromBill;
    }

    $rawLedger = [];

    // Get Purchases excluding Cheque Returns - NO DEBIT FROM supplier_pay
    $billsQuery = SupplierGRNMaster::where('supplier_name', $supplierName)
        ->where(function ($q) {
            $q->whereNull('description')
              ->orWhere('description', 'not like', '%Cheque Return%');
        });
    if ($startDate) $billsQuery->where('g_date', '>=', $startDate);
    if ($endDate) $billsQuery->where('g_date', '<=', $endDate);
    $bills = $billsQuery->orderBy('g_date')->get();

   foreach ($bills as $bill) {
    $credit = $bill->tobe_price ?? 0;
    $debit = $bill->supplier_pay ?? 0;

    $description = 'Purchase - GRN ' . $bill->grn_no;
    if ($debit > 0) {
        $description .= ' (Paid: ' . number_format($debit, 2) . ')';
    }

    $rawLedger[] = [
        'date' => $bill->g_date,
        'type' => 'purchase',
        'bill_no' => $bill->grn_no,
        'description' => $description,
        'debit' => $debit,
        'credit' => $credit,
        'is_return' => false,
    ];

    // **Skip adding individual payment rows for this GRN to avoid double counting**


        // ADD matching payment(s) row for this GRN from due payments table
        $matchedPayments = DB::table('supplier_due_payments')
            ->join('supplier_dues', 'supplier_due_payments.supplier_due_id', '=', 'supplier_dues.id')
            ->where('supplier_dues.supplier_name', $supplierName)
            ->where('supplier_dues.grn_no', $bill->grn_no) // match GRN
            ->get();

        foreach ($matchedPayments as $payment) {
            if ($payment->payment_method === 'Cheque') {
                $rawLedger[] = [
                    'date' => \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d'),
                    'type' => 'payment_cheque',
                    'bill_no' => $payment->cheque_number ?? '',
                    'description' => 'Cheque issued - Cheque #' . $payment->cheque_number,
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'is_return' => false,
                ];
            } else {
                $rawLedger[] = [
                    'date' => \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d'),
                    'type' => 'payment',
                    'bill_no' => '',
                    'description' => 'Payment by Cash',
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'is_return' => false,
                ];
            }
        }
    }

    $purchaseRows = collect($rawLedger)->where('type', 'purchase');
    $paymentRows = collect($rawLedger)->whereIn('type', ['payment', 'payment_cheque','cheque_return','payment']);

    // Get Payments excluding cheque returns
    $paymentsQuery = DB::table('supplier_due_payments')
        ->join('supplier_dues', 'supplier_due_payments.supplier_due_id', '=', 'supplier_dues.id')
        ->leftJoin('cheque_returns', 'supplier_due_payments.id', '=', 'cheque_returns.supplier_due_payment_id')
        ->where('supplier_dues.supplier_name', $supplierName)
        ->whereRaw("DATE(supplier_due_payments.created_at) > DATE(supplier_dues.created_at)");

    if ($startDate) $paymentsQuery->whereDate('supplier_due_payments.created_at', '>=', $startDate);
    if ($endDate) $paymentsQuery->whereDate('supplier_due_payments.created_at', '<=', $endDate);

    $payments = $paymentsQuery->orderBy('supplier_due_payments.created_at')->get();

    // Get returned cheques
    $returnedPaymentsQuery = DB::table('cheque_returns')
        ->join('supplier_due_payments', 'cheque_returns.supplier_due_payment_id', '=', 'supplier_due_payments.id')
        ->join('supplier_dues', 'supplier_due_payments.supplier_due_id', '=', 'supplier_dues.id')
        ->where('supplier_dues.supplier_name', $supplierName);

    if ($startDate) $returnedPaymentsQuery->whereDate('cheque_returns.return_date', '>=', $startDate);
    if ($endDate) $returnedPaymentsQuery->whereDate('cheque_returns.return_date', '<=', $endDate);

    $returnedPayments = $returnedPaymentsQuery->orderBy('cheque_returns.return_date')->get();
    $returnedPaymentIds = $returnedPayments->pluck('supplier_due_payment_id')->toArray();

    // Process all payments that are NOT already processed as GRN payments
    foreach ($payments as $payment) {
        if ($payment->payment_method === 'Cheque') {
            $rawLedger[] = [
                'date' => \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d'),
                'type' => 'payment_cheque',
                'bill_no' => $payment->cheque_number ?? '',
                'description' => 'Cheque issued - Cheque #' . $payment->cheque_number,
                'debit' => $payment->amount,
                'credit' => 0,
                'is_return' => false,
            ];

            // Add return row if cheque is returned
            if (in_array($payment->id, $returnedPaymentIds)) {
                $matchedReturn = $returnedPayments->firstWhere('supplier_due_payment_id', $payment->id);
                if ($matchedReturn) {
                    $rawLedger[] = [
                        'date' => $matchedReturn->return_date,
                        'type' => 'cheque_return',
                        'bill_no' => $matchedReturn->cheque_number,
                        'description' => 'Cheque Return - Cheque #' . $matchedReturn->cheque_number,
                        'debit' => 0,
                        'credit' => $matchedReturn->amount,
                        'is_return' => true,
                    ];
                }
            }
        } else {
            // Cash or other payments
            $rawLedger[] = [
                'date' => \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d'),
                'type' => 'payment',
                'bill_no' => '',
                'description' => 'Payment by Cash',
                'debit' => $payment->amount,
                'credit' => 0,
                'is_return' => false,
            ];
        }
    }

    // Cheque Returns - add as credit to reverse payment effect
    $returnedPaymentsQuery = DB::table('cheque_returns')
        ->join('supplier_due_payments', 'cheque_returns.supplier_due_payment_id', '=', 'supplier_due_payments.id')
        ->join('supplier_dues', 'supplier_due_payments.supplier_due_id', '=', 'supplier_dues.id')
        ->where('supplier_dues.supplier_name', $supplierName);

    if ($startDate) $returnedPaymentsQuery->whereDate('cheque_returns.return_date', '>=', $startDate);
    if ($endDate) $returnedPaymentsQuery->whereDate('cheque_returns.return_date', '<=', $endDate);

    $returnedPayments = $returnedPaymentsQuery->orderBy('cheque_returns.return_date')->get();

    foreach ($returnedPayments as $ret) {
        $rawLedger[] = [
            'date' => $ret->return_date,
            'type' => 'cheque_return',
            'bill_no' => $ret->cheque_number,
            'description' => 'Cheque Return - Cheque #' . $ret->cheque_number,
            'debit' => 0,
            'credit' => $ret->amount,
            'is_return' => true,
        ];
    }

    // Sort ledger by date and type (purchases first if same date)
    usort($rawLedger, function ($a, $b) {
        $dateA = strtotime($a['date']);
        $dateB = strtotime($b['date']);
        if ($dateA === $dateB) {
            if ($a['type'] === 'purchase') return -1;
            if ($b['type'] === 'purchase') return 1;
            return 0;
        }
        return $dateA <=> $dateB;
    });

    // Calculate running balance
    $ledger = [];
    $runningBalance = $openingBalance;
  $totalPaid = 0;
$totalToBePaid = 0;
$totalReturned = 0;
$runningBalance = $openingBalance;

foreach ($rawLedger as $entry) {
    $debit = is_numeric($entry['debit']) ? $entry['debit'] : 0;
    $credit = is_numeric($entry['credit']) ? $entry['credit'] : 0;

    if (!empty($entry['is_return'])) {
        // cheque return entry, don't change balance here
        $runningBalance = $runningBalance; 
    } else if ($entry['type'] === 'purchase') {
        // Only update balance for purchase rows
        $runningBalance = $runningBalance + $credit - $debit;

        // Track totals only for purchase rows
        if ($debit > 0) {
            $totalPaid += $debit;
        }
        if ($credit > 0) {
            $totalToBePaid += $credit;
        }
    }

    // Track total returned for all cheque return entries
    if (!empty($entry['is_return'])) {
        $totalReturned += $credit;
    }

    $ledger[] = [
        'date' => $entry['date'],
        'bill_no' => $entry['bill_no'],
        'description' => $entry['description'],
        'debit' => $debit,
        'credit' => $credit,
        'balance' => $runningBalance,
        'is_return' => $entry['is_return'] ?? false,
    ];
}

$finalBalance = $runningBalance;


    return view('ledger.supplier_ledger', compact(
        'ledger',
        'supplierName',
        'startDate',
        'endDate',
        'openingBalance',
        'totalPaid',
        'totalToBePaid',
        'totalReturned',
        'finalBalance',
        'purchaseRows',
        'paymentRows'
    ));
}


//supplier serach//
public function supplierSearch(Request $request)
{
    $query = $request->get('query', '');
    if (!$query) return response()->json([]);

    $suppliers = Supplier::where('id', 'ILIKE', "%{$query}%")
        ->orWhere('supplier_name', 'ILIKE', "%{$query}%")
        ->limit(10)
        ->get(['id', 'supplier_name']);

    return response()->json($suppliers);
}



//supplier ledger pdf export//
public function exportSupplierLedgerPDF(Request $request)
{
    // Reuse logic from supplierLedger
    $supplierId = $request->input('supplier_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $supplier = Supplier::find($supplierId);
    if (!$supplier) {
        return back()->withErrors(['supplier_id' => 'Invalid supplier ID']);
    }

    $supplierName = $supplier->supplier_name;

    $openingBalanceQuery = SupplierGRNMaster::where('supplier_name', $supplierName);
    if ($startDate) {
        $openingBalanceQuery->where('g_date', '<', $startDate);
    }

    $openingTransactions = $openingBalanceQuery->get();
    $openingBalance = 0;
    foreach ($openingTransactions as $transaction) {
        $debit = $transaction->supplier_pay ?? 0;
        $credit = $transaction->tobe_price ?? 0;
        $openingBalance += ($credit - $debit);
    }

    $billsQuery = SupplierGRNMaster::where('supplier_name', $supplierName);
    if ($startDate) $billsQuery->where('g_date', '>=', $startDate);
    if ($endDate) $billsQuery->where('g_date', '<=', $endDate);

    $bills = $billsQuery->orderBy('g_date')->get();

    $ledger = [];
    $runningBalance = $openingBalance;

    $ledger[] = [
        'date' => $startDate ? date('Y-m-d', strtotime($startDate . ' -1 day')) : null,
        'bill_no' => '',
        'description' => 'Opening Balance',
        'debit' => '',
        'credit' => '',
        'balance' => $openingBalance,
    ];

    foreach ($bills as $bill) {
        $debit = $bill->supplier_pay ?? 0;
        $credit = $bill->tobe_price ?? 0;

        $runningBalance += ($credit - $debit);

        $ledger[] = [
            'date' => $bill->g_date,
            'bill_no' => $bill->grn_no,
            'description' => 'Purchase - GRN ' . $bill->grn_no,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $runningBalance,
        ];
    }

    $pdf = Pdf::loadView('ledger.supplier_ledger_pdf', [
        'ledger' => $ledger,
        'supplierName' => $supplierName,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'openingBalance' => $openingBalance,
    ]);

    return $pdf->stream('supplier_ledger_' . now()->format('Ymd_His') . '.pdf');
}

}
