<?php

// app/Http/Controllers/ItemSummaryController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemSummary;
use PDF;

class ItemSummaryController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemSummary::query();

        // Date range filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('grn_date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('grn_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('grn_date', '<=', $request->end_date);
        }

        // Search filter on item_code or item_name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        $summaries = $query->orderBy('bill_no')->orderBy('item_code')
            ->paginate(20)
            ->withQueryString();

        $allFiltered = (clone $query)->get();

        $itemTotals = $allFiltered->groupBy('item_code')->map(function ($rows) {
            return [
                'item_name' => $rows->first()->item_name,
                'quantity' => $rows->sum('quantity'),
                'total_price' => $rows->sum('total_price'),
            ];
        });

        $allItems = \App\Models\Item::select('item_code', 'item_name')->distinct()->get();
return view('item_summaries.index', compact('summaries', 'itemTotals', 'allItems'));

    }

    public function summarydownloadPDF(Request $request)
    {
        $query = ItemSummary::query();

        // Apply  filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('grn_date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('grn_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('grn_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        $summaries = $query->orderBy('bill_no')->orderBy('item_code')->get();

        $itemTotals = $summaries->groupBy('item_code')->map(function ($rows) {
            return [
                'item_name' => $rows->first()->item_name,
                'quantity' => $rows->sum('quantity'),
                'total_price' => $rows->sum('total_price'),
            ];
        });

        $pdf = PDF::loadView('item_summaries.pdf', compact('itemTotals', 'request'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('item_summary_report.pdf');
    }
}
