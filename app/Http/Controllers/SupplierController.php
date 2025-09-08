<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    'supplier_id' => 'required|integer|unique:supplier,supplier_id',
    'supplier_name' => 'required|string',
    'phone' => 'required|string',
    'address' => 'required|string',


]);

        Supplier::create($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier added successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_id' => 'required|integer|unique:supplier,supplier_id,' . $supplier->id,
            'supplier_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $supplier->update($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully.');
    }

//supplier leager card//
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

    $openingBalanceQuery = SupplierGRNMaster::where('supplier_name', $supplierName);
    if ($startDate) {
        $openingBalanceQuery->where('g_date', '<', $startDate);
    }

    $openingTransactions = $openingBalanceQuery->get();
    $openingBalance = 0;
    foreach ($openingTransactions as $transaction) {
        $debit = $transaction->supplier_pay ?? 0;
        $credit = $transaction->tobe_price ?? 0;
        $openingBalance += ($credit - $debit); // For supplier, tobe_price = Credit, pay = Debit
    }

    $billsQuery = SupplierGRNMaster::where('supplier_name', $supplierName);
    if ($startDate) $billsQuery->where('g_date', '>=', $startDate);
    if ($endDate) $billsQuery->where('g_date', '<=', $endDate);

    $bills = $billsQuery->orderBy('g_date')->get();

    $ledger = [];
    $runningBalance = $openingBalance;

    $ledger[] = [
'date' => $startDate ? Carbon::parse($startDate)->subDay()->format('Y-m-d') : null,
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

    return view('ledger.supplier_ledger', compact('ledger', 'supplierName', 'startDate', 'endDate', 'openingBalance'));
}

//supplier serach//
public function supplierSearch(Request $request)
{
    $query = $request->get('query', '');
    if (!$query) return response()->json([]);

    $suppliers = Supplier::where('id', 'like', "%{$query}%")
        ->orWhere('supplier_name', 'like', "%{$query}%")
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
