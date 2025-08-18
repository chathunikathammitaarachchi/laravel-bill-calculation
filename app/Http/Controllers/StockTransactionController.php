<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use App\Models\StockInHand;
use Illuminate\Support\Facades\DB;
use PDF;

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
        $values = [120, 75]; // Dummy values
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

    // ✅ NEW METHOD to handle stock transaction and update StockInHand
    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string',
            'item_name' => 'required|string',
            'transaction_type' => 'required|in:IN,OUT',
            'quantity' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        // Create stock transaction
        $transaction = StockTransaction::create([
            'item_code' => $request->item_code,
            'item_name' => $request->item_name,
            'transaction_type' => $request->transaction_type,
            'quantity' => $request->quantity,
            'transaction_date' => $request->transaction_date,
        ]);

        // 🔄 Update StockInHand summary
        $stockIn = StockTransaction::where('item_code', $request->item_code)->where('transaction_type', 'IN')->sum('quantity');
        $stockOut = StockTransaction::where('item_code', $request->item_code)->where('transaction_type', 'OUT')->sum('quantity');
        $balance = $stockIn - $stockOut;

        $existing = StockInHand::where('item_code', $request->item_code)->first();

        if ($existing) {
            $existing->update([
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'stock_balance' => $balance,
            ]);
        } else {
            StockInHand::create([
                'item_code' => $request->item_code,
                'item_name' => $request->item_name,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'stock_balance' => $balance,
            ]);
        }

        return redirect()->back()->with('success', 'Stock transaction added and stock in hand updated successfully.');
    }
}
