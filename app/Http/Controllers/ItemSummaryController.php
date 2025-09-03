<?php

// app/Http/Controllers/ItemSummaryController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemSummary;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemSummaryController extends Controller
{
public function index(Request $request)
{
    $query = ItemSummary::query()
        ->when($request->start_date, fn($q) => $q->whereDate('grn_date', '>=', $request->start_date))
        ->when($request->end_date, fn($q) => $q->whereDate('grn_date', '<=', $request->end_date))
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($q2) use ($search) {
                $q2->where('item_code', 'like', "%{$search}%")
                   ->orWhere('item_name', 'like', "%{$search}%");
            });
        });

    $filtersApplied = $request->start_date || $request->end_date || $request->search;

    // Paginate the filtered data (pagination based on individual records)
    $paginatedSummaries = $query->orderBy('grn_date', 'desc')->paginate(15);

    // Group filtered data by item_code and date (for daily sales per item)
    $allFilteredData = $query->orderBy('grn_date', 'desc')->get();

    $dailySummary = $allFilteredData->groupBy(function ($item) {
        return $item->item_code . '|' . $item->grn_date->toDateString();
    })->map(function ($group, $key) {
        [$code, $date] = explode('|', $key);
        return [
            'item_code' => $code,
            'item_name' => $group->first()->item_name,
            'date' => $date,
            'quantity' => $group->sum('quantity'),
            'total_price' => $group->sum('total_price'),
        ];
    })->values();

    // Get distinct items for datalist autocomplete
    $allItems = ItemSummary::select('item_code', 'item_name')->distinct()->get();

    return view('item_summaries.index', [
        'summaries' => $paginatedSummaries,
        'dailySummary' => $dailySummary,
        'allItems' => $allItems,
        'filtersApplied' => $filtersApplied,
        'request' => $request,
    ]);
}



    public function summarydownloadPDF(Request $request)
{
    $chartImage = $request->input('chartData');
    $lineChartImage = $request->input('lineChartData');

    $start_date = $request->input('start_date') ?? 'N/A';
    $end_date = $request->input('end_date') ?? 'N/A';
    $search = $request->input('search') ?? 'N/A';

    $query = ItemSummary::query()
        ->when($request->start_date, fn($q) => $q->whereDate('grn_date', '>=', $request->start_date))
        ->when($request->end_date, fn($q) => $q->whereDate('grn_date', '<=', $request->end_date))
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($q2) use ($search) {
                $q2->where('item_code', 'like', "%{$search}%")
                   ->orWhere('item_name', 'like', "%{$search}%");
            });
        });

    $allFilteredData = $query->orderBy('grn_date', 'desc')->get();

    $dailySummary = $allFilteredData->groupBy(function ($item) {
        return $item->item_code . '|' . $item->grn_date->toDateString();
    })->map(function ($group, $key) {
        [$code, $date] = explode('|', $key);
        return [
            'item_code' => $code,
            'item_name' => $group->first()->item_name,
            'date' => $date,
            'quantity' => $group->sum('quantity'),
            'total_price' => $group->sum('total_price'),
        ];
    })->values();

    $pdf = PDF::loadView('item_summaries.pdf', [
        'start_date' => $start_date,
        'end_date' => $end_date,
        'search' => $search,
        'dailySummary' => $dailySummary,
        'barChartImage' => $chartImage,
        'lineChartImage' => $lineChartImage,
    ]);

    return $pdf->stream('item-summary-report.pdf');
}


}
