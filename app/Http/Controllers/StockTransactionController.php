<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Support\Facades\DB;
use PDF;

class StockTransactionController extends Controller
{
    
  public function index(Request $request)
{
    $type = $request->query('type');
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    $transactions = StockTransaction::when($type, function ($query, $type) {
            return $query->where('transaction_type', $type);
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->orderBy('transaction_date', 'desc')
        ->get();

    $summary = StockTransaction::select('transaction_type', DB::raw('SUM(quantity) as total_quantity'))
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->groupBy('transaction_type')
        ->pluck('total_quantity', 'transaction_type');

    return view('stock_transactions.index', compact('transactions', 'summary', 'startDate', 'endDate', 'type'));
}

public function showPieChart(Request $request)
{
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    $data = StockTransaction::select('transaction_type', DB::raw('SUM(quantity) as total_quantity'))
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('transaction_date', [$startDate, $endDate]);
        })
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

    $pdf = Pdf::loadView('stock_transactions.stock_pdf', compact('labels', 'values', 'startDate', 'endDate'))
          ->setPaper('a4', 'portrait');


    return $pdf->download('stock-in-out-report.pdf');
}
    
}
