<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use Carbon\Carbon;
use PDF; 

class DailyItemSummaryController extends Controller
{
public function index(Request $request)
{
    $start = $request->input('start_date');
    $end = $request->input('end_date');

    $query = StockTransaction::query();

    if ($start && $end) {
        $query->whereBetween('transaction_date', [$start, $end]);
    }

    $transactions = $query->where('transaction_type', 'OUT')
        ->orderBy('transaction_date')
        ->orderBy('id')
        ->get();

    $groupedByDate = [];
    $totals = [];

    foreach ($transactions as $tx) {
        $date = Carbon::parse($tx->transaction_date)->format('Y-m-d');
        $code = $tx->item_code;

        if (!isset($groupedByDate[$date][$code])) {
            $groupedByDate[$date][$code] = [
                'item_name' => $tx->item_name ?? 'Unknown',
                'quantity_total' => 0,
            ];
        }

        $groupedByDate[$date][$code]['quantity_total'] += $tx->quantity;

        // Collect totals for global max/min
        if (!isset($totals[$code])) {
            $totals[$code] = [
                'item_name' => $tx->item_name ?? 'Unknown',
                'quantity_total' => 0,
            ];
        }

        $totals[$code]['quantity_total'] += $tx->quantity;
    }

    // Get max & min item based on total quantity across all dates
    $maxItem = null;
    $minItem = null;

    foreach ($totals as $code => $data) {
        if (!$maxItem || $data['quantity_total'] > $maxItem['quantity_total']) {
            $maxItem = [
                'item_code' => $code,
                'item_name' => $data['item_name'],
                'quantity_total' => $data['quantity_total']
            ];
        }
        if (!$minItem || $data['quantity_total'] < $minItem['quantity_total']) {
            $minItem = [
                'item_code' => $code,
                'item_name' => $data['item_name'],
                'quantity_total' => $data['quantity_total']
            ];
        }
    }

    // Sort dates
    ksort($groupedByDate);

    // Sort items by quantity_total descending per date
    foreach ($groupedByDate as $date => $items) {
        uasort($items, fn($a, $b) => $b['quantity_total'] <=> $a['quantity_total']);
        $groupedByDate[$date] = $items;
    }

    return view('stock_history.itemsummary', [
        'groupedByDate' => $groupedByDate,
        'maxItem' => $maxItem,
        'minItem' => $minItem,
    ]);
}


public function itemsummarydownloadPdf(Request $request)
{
    $start = $request->input('start_date');
    $end = $request->input('end_date');

    $query = StockTransaction::query();

    if ($start && $end) {
        $query->whereBetween('transaction_date', [$start, $end]);
    }

    $transactions = $query->where('transaction_type', 'OUT')
        ->orderBy('transaction_date')
        ->orderBy('id')
        ->get();

    $groupedByDate = [];
    $totals = [];

    foreach ($transactions as $tx) {
        $date = Carbon::parse($tx->transaction_date)->format('Y-m-d');
        $code = $tx->item_code;

        // Grouping by date
        if (!isset($groupedByDate[$date][$code])) {
            $groupedByDate[$date][$code] = [
                'item_name' => $tx->item_name ?? 'Unknown',
                'quantity_total' => 0,
            ];
        }

        $groupedByDate[$date][$code]['quantity_total'] += $tx->quantity;

        // Totals for min/max across all days
        if (!isset($totals[$code])) {
            $totals[$code] = [
                'item_name' => $tx->item_name ?? 'Unknown',
                'quantity_total' => 0,
            ];
        }

        $totals[$code]['quantity_total'] += $tx->quantity;
    }

    // Sort dates ascending
    ksort($groupedByDate);

    // Sort each date's items by total quantity DESC
    foreach ($groupedByDate as $date => $items) {
        uasort($items, fn($a, $b) => $b['quantity_total'] <=> $a['quantity_total']);
        $groupedByDate[$date] = $items;
    }

    // Find max and min item overall
    $maxItem = null;
    $minItem = null;

    foreach ($totals as $code => $data) {
        if (!$maxItem || $data['quantity_total'] > $maxItem['quantity_total']) {
            $maxItem = [
                'item_code' => $code,
                'item_name' => $data['item_name'],
                'quantity_total' => $data['quantity_total']
            ];
        }

        if (!$minItem || $data['quantity_total'] < $minItem['quantity_total']) {
            $minItem = [
                'item_code' => $code,
                'item_name' => $data['item_name'],
                'quantity_total' => $data['quantity_total']
            ];
        }
    }

    $pdf = PDF::loadView('stock_history.itemsummary_pdf', [
        'groupedByDate' => $groupedByDate,
        'maxItem' => $maxItem,
        'minItem' => $minItem,
        'start_date' => $start,
        'end_date' => $end
    ]);

    $fileName = 'daily_item_summary_' . ($start ?? 'all') . '_to_' . ($end ?? 'all') . '.pdf';

    return $pdf->download($fileName);
}

}
