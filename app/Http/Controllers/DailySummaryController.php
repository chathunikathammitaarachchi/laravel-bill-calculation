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

    // Get Opening Balances BEFORE start date
    $openingBalances = StockTransaction::select(
        'item_code',
        'item_name',
        DB::raw("SUM(CASE WHEN transaction_type = 'IN' THEN quantity ELSE 0 END) as opening_in"),
        DB::raw("SUM(CASE WHEN transaction_type = 'OUT' THEN quantity ELSE 0 END) as opening_out")
    )
    ->when($startDate, function ($query) use ($startDate) {
        return $query->where('transaction_date', '<', $startDate);
    })
    ->groupBy('item_code', 'item_name')
    ->get();

    // Get Transactions WITHIN the date range
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

    // Combine into view
    return view('daily_summary.index', compact('dailySummary', 'startDate', 'endDate', 'openingBalances'));
}






public function dailydownloadPdf(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Opening Balances before start date
    $openingBalances = StockTransaction::select(
        'item_code',
        'item_name',
        DB::raw("SUM(CASE WHEN transaction_type = 'IN' THEN quantity ELSE 0 END) as opening_in"),
        DB::raw("SUM(CASE WHEN transaction_type = 'OUT' THEN quantity ELSE 0 END) as opening_out")
    )
    ->when($startDate, function ($query) use ($startDate) {
        return $query->where('transaction_date', '<', $startDate);
    })
    ->groupBy('item_code', 'item_name')
    ->get();

    // Transactions within range
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


 $pieChartImage = $request->input('pie_chart_image');
    $barChartImage = $request->input('bar_chart_image');


    $pdf = PDF::loadView('daily_summary.pdf', [
        'dailySummary' => $dailySummary,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'openingBalances' => $openingBalances,
         'pieChartImage' => $pieChartImage,
        'barChartImage' => $barChartImage
    ]);

    return $pdf->download('Daily_Stock_Summary.pdf');
}







}
