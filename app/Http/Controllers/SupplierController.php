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

    // ===== Opening Balance =====
    $openingBalanceQuery = SupplierGRNMaster::where('supplier_name', $supplierName);
    if ($startDate) {
        $openingBalanceQuery->where('g_date', '<', $startDate);
    }

    $openingBalance = 0;
    foreach ($openingBalanceQuery->get() as $transaction) {
        $debit = $transaction->supplier_pay ?? 0;
        $credit = $transaction->tobe_price ?? 0;
        $openingBalance += ($credit - $debit);
    }

    // ===== GRN Entries (excluding cheque return descriptions) =====
    $billsQuery = SupplierGRNMaster::where('supplier_name', $supplierName)
        ->where(function ($query) {
            $query->whereNull('description')
                  ->orWhere('description', 'not like', '%Cheque Return%');
        });

    if ($startDate) $billsQuery->where('g_date', '>=', $startDate);
    if ($endDate) $billsQuery->where('g_date', '<=', $endDate);

    $bills = $billsQuery->orderBy('g_date')->get();

    // ===== Cheque Return Entries (Individual entries with cheque numbers) =====
    $returnedPaymentsQuery = DB::table('cheque_returns')
        ->join('supplier_due_payments', 'cheque_returns.supplier_due_payment_id', '=', 'supplier_due_payments.id')
        ->join('supplier_dues', 'supplier_due_payments.supplier_due_id', '=', 'supplier_dues.id')
        ->where('supplier_dues.supplier_name', $supplierName)
        ->where('supplier_due_payments.payment_method', 'Cheque')
        ->select(
            'cheque_returns.*',
            'supplier_due_payments.amount',
            'supplier_due_payments.cheque_number',
            'supplier_due_payments.created_at as payment_date'
        );

    if ($startDate) $returnedPaymentsQuery->whereDate('cheque_returns.return_date', '>=', $startDate);
    if ($endDate) $returnedPaymentsQuery->whereDate('cheque_returns.return_date', '<=', $endDate);

    $returnedPayments = $returnedPaymentsQuery->orderBy('cheque_returns.return_date')->get();

    // ===== Build Ledger =====
    $ledger = [];
    $runningBalance = $openingBalance;

    // Opening balance entry
    if ($startDate) {
        $ledger[] = [
            'date' => \Carbon\Carbon::parse($startDate)->subDay()->format('Y-m-d'),
            'bill_no' => '',
            'description' => 'Opening Balance',
            'debit' => '',
            'credit' => '',
            'balance' => $runningBalance,
        ];
    }

    // Process each GRN bill
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

    // Add individual cheque return entries
    foreach ($returnedPayments as $returnedPayment) {
        $retAmt = $returnedPayment->amount;
        $chequeNumber = $returnedPayment->cheque_number ?? 'N/A';
        $returnDate = $returnedPayment->return_date;

        $runningBalance += $retAmt; // Add back the returned amount

        $ledger[] = [
            'date' => $returnDate,
            'bill_no' => $chequeNumber,
            'description' => 'Cheque Return - Cheque #' . $chequeNumber,
            'debit' => -1 * $retAmt, // Negative to show reversal of payment
            'credit' => 0,
            'balance' => $runningBalance,
        ];
    }

    // ===== Cash Payments =====
    $cashPaymentsQuery = DB::table('supplier_due_payments')
        ->join('supplier_dues', 'supplier_due_payments.supplier_due_id', '=', 'supplier_dues.id')
        ->where('supplier_dues.supplier_name', $supplierName)
        ->where('supplier_due_payments.payment_method', 'Cash');

    if ($startDate) $cashPaymentsQuery->whereDate('supplier_due_payments.created_at', '>=', $startDate);
    if ($endDate) $cashPaymentsQuery->whereDate('supplier_due_payments.created_at', '<=', $endDate);

    $cashPayments = $cashPaymentsQuery->orderBy('supplier_due_payments.created_at')->get();

    foreach ($cashPayments as $cashPayment) {
        $paymentDate = \Carbon\Carbon::parse($cashPayment->created_at)->format('Y-m-d');
        $amount = $cashPayment->amount;

        $runningBalance -= $amount;

        $ledger[] = [
            'date' => $paymentDate,
            'bill_no' => '',
            'description' => 'Payment by Cash',
            'debit' => $amount,
            'credit' => 0,
            'balance' => $runningBalance,
        ];
    }

    // Final sort by date and time
    usort($ledger, function ($a, $b) {
        $dateA = strtotime($a['date']);
        $dateB = strtotime($b['date']);
        
        if ($dateA == $dateB) {
            // If same date, put purchases before returns
            if (strpos($a['description'], 'Purchase') !== false) return -1;
            if (strpos($b['description'], 'Purchase') !== false) return 1;
            return 0;
        }
        
        return $dateA <=> $dateB;
    });

    return view('ledger.supplier_ledger', compact('ledger', 'supplierName', 'startDate', 'endDate', 'openingBalance'));
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
