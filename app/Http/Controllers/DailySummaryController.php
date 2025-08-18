<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use App\Models\DailyStockSummary;
use Illuminate\Support\Facades\DB;
use PDF; 

class DailySummaryController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

      $dailySummary = StockTransaction::select(
        'transaction_date',
        'item_code',
        'item_name',
        DB::raw("SUM(CASE WHEN transaction_type = 'IN' THEN quantity ELSE 0 END) as stock_in"),
        DB::raw("SUM(CASE WHEN transaction_type = 'OUT' THEN quantity ELSE 0 END) as stock_out")
    )
    ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    })
    ->groupBy('transaction_date', 'item_code', 'item_name')
    ->orderBy('transaction_date', 'desc')
    ->orderBy('item_code')
    ->get();


     foreach ($dailySummary as $day) {
    // Skip if item_code or item_name is null
    if (empty($day->item_code) || empty($day->item_name)) {
        continue;
    }

    DailyStockSummary::updateOrCreate(
        [
            'transaction_date' => $day->transaction_date,
            'item_code' => $day->item_code,
        ],
        [
            'item_name' => $day->item_name,
            'stock_in' => $day->stock_in,
            'stock_out' => $day->stock_out,
        ]
    );
}



        return view('daily_summary.index', compact('dailySummary', 'startDate', 'endDate'));
    }






public function dailydownloadPdf(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $dailySummary = StockTransaction::select(
        'transaction_date',
        'item_code',
        'item_name',
        DB::raw("SUM(CASE WHEN transaction_type = 'IN' THEN quantity ELSE 0 END) as stock_in"),
        DB::raw("SUM(CASE WHEN transaction_type = 'OUT' THEN quantity ELSE 0 END) as stock_out")
    )
    ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    })
    ->groupBy('transaction_date', 'item_code', 'item_name')
    ->orderBy('transaction_date', 'desc')
    ->orderBy('item_code')
    ->get();

    $pdf = PDF::loadView('daily_summary.pdf', [
        'dailySummary' => $dailySummary,
        'startDate' => $startDate,
        'endDate' => $endDate
    ]);

    return $pdf->download('Daily_Stock_Summary.pdf');
}






}
