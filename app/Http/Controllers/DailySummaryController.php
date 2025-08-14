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
                'source',
                DB::raw("SUM(CASE WHEN transaction_type = 'IN' THEN quantity ELSE 0 END) as stock_in"),
                DB::raw("SUM(CASE WHEN transaction_type = 'OUT' THEN quantity ELSE 0 END) as stock_out")
            )
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->groupBy('transaction_date', 'source')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('source')
            ->get();

        foreach ($dailySummary as $day) {
            DailyStockSummary::updateOrCreate(
                [
                    'transaction_date' => $day->transaction_date,
                    'source' => $day->source,
                ],
                [
                    'stock_in' => $day->stock_in,
                    'stock_out' => $day->stock_out,
                ]
            );
        }

        return view('daily_summary.index', compact('dailySummary', 'startDate', 'endDate'));
    }





public function dailydownloadPDF(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $dailySummary = StockTransaction::select(
            'transaction_date',
            'source',
            DB::raw("SUM(CASE WHEN transaction_type = 'IN' THEN quantity ELSE 0 END) as stock_in"),
            DB::raw("SUM(CASE WHEN transaction_type = 'OUT' THEN quantity ELSE 0 END) as stock_out")
        )
        ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
            return $query->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->groupBy('transaction_date', 'source')
        ->orderBy('transaction_date', 'desc')
        ->orderBy('source')
        ->get();

    $pdf = PDF::loadView('daily_summary.pdf', compact('dailySummary', 'startDate', 'endDate'));

    return $pdf->download('daily_summary_' . now()->format('Y_m_d_His') . '.pdf');
}






}
