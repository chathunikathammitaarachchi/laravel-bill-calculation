<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use App\Models\StockInHands; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $transactions = StockTransaction::when($type, fn($q) => $q->where('transaction_type', $type))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->orderBy('transaction_date', 'desc')
            ->get();

        $summary = StockTransaction::select('transaction_type', DB::raw('SUM(quantity) as total_quantity'))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->groupBy('transaction_type')
            ->pluck('total_quantity', 'transaction_type');

        return view('stock_transactions.index', compact('transactions', 'summary', 'startDate', 'endDate', 'type'));
    }

    public function showPieChart(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $data = StockTransaction::select('transaction_type', DB::raw('SUM(quantity) as total_quantity'))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->groupBy('transaction_type')
            ->get();

        $labels = $data->pluck('transaction_type')->toArray();
        $values = $data->pluck('total_quantity')->toArray();

        return view('stock_transactions.pie_chart', compact('labels', 'values', 'startDate', 'endDate'));
    }

    public function stockexportPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type');

        $query = StockTransaction::query();

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        if ($type) {
            $query->where('transaction_type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $summary = [
            'IN' => $transactions->where('transaction_type', 'IN')->sum('quantity'),
            'OUT' => $transactions->where('transaction_type', 'OUT')->sum('quantity'),
        ];

        $pdf = PDF::loadView('stock_transactions.report_pdf', compact('transactions', 'startDate', 'endDate', 'type', 'summary'));
        $fileName = 'stock_transactions_report_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function downloadPdf(Request $request)
    {
        $labels = ['IN', 'OUT'];
        $values = [120, 75]; 
        $startDate = $request->start_date ?? '2025-01-01';
        $endDate = $request->end_date ?? '2025-08-01';

        $pdf = PDF::loadView('stock_transactions.stock_pdf', compact('labels', 'values', 'startDate', 'endDate'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('stock-in-out-report.pdf');
    }

    public function transaction(Request $request)
    {
        $type = $request->query('type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $transactions = StockTransaction::when($type, fn($q) => $q->where('transaction_type', $type))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->orderBy('transaction_date', 'desc')
            ->get();

        $summary = StockTransaction::select('transaction_type', DB::raw('SUM(quantity) as total_quantity'))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->groupBy('transaction_type')
            ->pluck('total_quantity', 'transaction_type');

        $dailySummary = StockTransaction::select(
                'transaction_date',
                DB::raw("SUM(CASE WHEN transaction_type='IN' THEN quantity ELSE 0 END) as stock_in"),
                DB::raw("SUM(CASE WHEN transaction_type='OUT' THEN quantity ELSE 0 END) as stock_out")
            )
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->groupBy('transaction_date')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('stock_transactions.transaction', compact(
            'transactions', 'summary', 'dailySummary', 'startDate', 'endDate', 'type'
        ));
    }
public function store(Request $request)
{
    $request->validate([
        'item_code' => 'required|numeric',
        'item_name' => 'required|string',
        'transaction_type' => 'required|in:IN,OUT',
        'quantity' => 'required|numeric|min:1',
        'reference_no' => 'required|string',
        'source' => 'required|string',
        'transaction_date' => 'required|date',
    ]);

    $quantity = abs($request->quantity); // positive quantity
    $type = $request->transaction_type;
    $itemCode = $request->item_code;

    // Pass positive quantity, model will handle sign
    StockTransaction::create([
        'item_code' => $itemCode,
        'item_name' => $request->item_name,
        'transaction_type' => $type,
        'quantity' => $quantity,
        'reference_no' => $request->reference_no,
        'source' => $request->source,
        'transaction_date' => $request->transaction_date,
    ]);

    // Now calculate sums carefully: use absolute value for stock_in and stock_out
    $stockIn = StockTransaction::where('item_code', $itemCode)
        ->where('transaction_type', 'IN')
        ->sum(DB::raw('ABS(quantity)'));
    $stockOut = StockTransaction::where('item_code', $itemCode)
        ->where('transaction_type', 'OUT')
        ->sum(DB::raw('ABS(quantity)'));

    $balance = $stockIn - $stockOut;

    $existing = StockInHands::where('item_code', $itemCode)->first();

    if ($existing) {
        $existing->update([
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
            'stock_balance' => $balance,
        ]);
    } else {
        StockInHands::create([
            'item_code' => $itemCode,
            'item_name' => $request->item_name,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
            'stock_balance' => $balance,
        ]);
    }

    return redirect()->back()->with('success', 'Stock transaction added and stock in hand updated successfully.');
}




//stock in hand

public function stockInHandIndex(Request $request)
{
    $date = $request->input('date');

    // If no datetime provided, use current datetime
    $selectedDate = $date ? Carbon::parse($date) : Carbon::now();

    $itemCodes = StockTransaction::distinct()->pluck('item_code');

    // Clear old stock_in_hand data (optional)
    StockInHands::truncate();

    foreach ($itemCodes as $itemCode) {
        $stockIn = StockTransaction::where('item_code', $itemCode)
            ->where('transaction_type', 'IN')
            ->where('created_at', '<=', $selectedDate)
            ->sum(DB::raw('ABS(quantity)'));

        $stockOut = -1 * StockTransaction::where('item_code', $itemCode)
            ->where('transaction_type', 'OUT')
            ->where('created_at', '<=', $selectedDate)
            ->sum(DB::raw('ABS(quantity)'));

        $balance = $stockIn + $stockOut;

        $itemName = StockTransaction::where('item_code', $itemCode)->value('item_name');

        // Save to StockInHands
        if ($stockIn > 0 || $stockOut < 0) {
            StockInHands::updateOrCreate(
                ['item_code' => $itemCode],
                [
                    'item_name' => $itemName,
                    'stock_in' => $stockIn,
                    'stock_out' => $stockOut,
                    'stock_balance' => $balance,
                ]
            );
        }
    }

    $stockInHands = StockInHands::all();

    return view('stock_in_hand.index', compact('stockInHands', 'date'));
}


public function binCard(Request $request)
{
    $validated = $request->validate([
        'search' => 'nullable|string',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
    ]);

    $search = $validated['search'] ?? null;
    $startDate = $validated['start_date'] ?? null;
    $endDate = $validated['end_date'] ?? null;

    // Calculate Opening Balance BEFORE the start date
    $openingBalance = 0;
    if ($startDate) {
        $previousTransactions = StockTransaction::query()
            ->when($search, fn($q) => $q->where('item_code', $search))
            ->where('transaction_date', '<', $startDate)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($previousTransactions as $transaction) {
            $qty = abs($transaction->quantity);
            $in = $transaction->transaction_type === 'IN' ? $qty : 0;
            $out = $transaction->transaction_type === 'OUT' ? $qty : 0;
            $openingBalance += ($in - $out);
        }
    }

    // Get transactions within the selected date range
    $transactions = StockTransaction::query()
        ->when($search, fn($q) => $q->where('item_code', $search))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
        ->orderBy('transaction_date', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    // If no transactions AND no opening balance, show empty view
    if ($transactions->isEmpty() && $openingBalance === 0) {
        return view('stock_transactions.bin_card', [
            'binCard' => [],
            'itemCode' => $search,
            'itemName' => '',
            'search' => $search,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalIn' => 0,
            'totalOut' => 0,
            'finalBalance' => 0,
        ]);
    }

    $itemName = $transactions->first()->item_name ?? '';
    $itemCode = $transactions->first()->item_code ?? $search;

    $runningBalance = $openingBalance;
    $binCard = [];

    // Add opening balance row
    if ($startDate) {
        $binCard[] = [
            'date' => $startDate,
            'source' => 'Opening Balance',
            'quantity_in' => 0,
            'quantity_out' => 0,
            'balance' => $runningBalance,
            'item_name' => $itemName,
        ];
    }

    // Loop over transactions & calculate running balance
    foreach ($transactions as $transaction) {
        $qty = abs($transaction->quantity);
        $in = $transaction->transaction_type === 'IN' ? $qty : 0;
        $out = $transaction->transaction_type === 'OUT' ? $qty : 0;

        $runningBalance += ($in - $out);

        $binCard[] = [
            'date' => $transaction->transaction_date,
            'source' => $transaction->source,
            'quantity_in' => $in,
            'quantity_out' => $out,
            'balance' => $runningBalance,
            'item_name' => $transaction->item_name,
        ];
    }

    $totalIn = collect($binCard)->sum('quantity_in');
    $totalOut = collect($binCard)->sum('quantity_out');
    $finalBalance = !empty($binCard) ? collect($binCard)->last()['balance'] : $openingBalance;

    return view('stock_transactions.bin_card', compact(
        'binCard', 'itemCode', 'itemName', 'search', 'startDate', 'endDate', 'totalIn', 'totalOut', 'finalBalance'
    ));
}



}
