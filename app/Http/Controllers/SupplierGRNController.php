<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierGRNMaster;
use App\Models\SupplierGRNDetails;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\ItemPrice;
use Carbon\Carbon;

use App\Models\Supplier;


class SupplierGRNController extends Controller
{
     public function create()
    {
        $last = SupplierGRNMaster::orderBy('grn_no', 'desc')->first();
        $nextGrnNo = $last ? intval($last->grn_no) + 1 : 1;

        $suppliers = Supplier::all();

        // Load items with their prices (rate and cost_price)
        $items = Item::with('itemPrices')->get();

        return view('bill.create', compact('nextGrnNo', 'suppliers', 'items'));
    }

public function store(Request $request)
{
    $request->validate([
        'grn_no' => 'required|unique:grnmaster,grn_no', // ✅ Confirmed table name
        'g_date' => 'required|date',
        'supplier_name' => 'required|string',
        'total_price' => 'required|numeric',
        'tobe_price' => 'required|numeric',
        'total_discount' => 'required|numeric',
        'supplier_pay' => 'required|numeric',
        'balance' => 'required|numeric',

        'items.*.item_code' => 'required|integer',
        'items.*.item_name' => 'required|string',
        'items.*.rate' => 'required|numeric',
        'items.*.cost_price' => 'required|numeric',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric',
    ]);

    DB::transaction(function () use ($request) {
        // ✅ Create GRN Master
        $master = SupplierGRNMaster::create($request->only([
            'grn_no', 'g_date', 'supplier_name', 'total_price',
            'tobe_price', 'total_discount', 'supplier_pay', 'balance',
        ]));

        // ✅ Process each GRN item
        foreach ($request->items as $grnItem) {
            // Create GRN detail
            SupplierGRNDetails::create([
                'grn_no'     => $master->grn_no,
                'item_code'  => $grnItem['item_code'],
                'item_name'  => $grnItem['item_name'],
                'rate'       => $grnItem['rate'],
                'cost_price' => $grnItem['cost_price'],
                'quantity'   => $grnItem['quantity'],
                'price'      => $grnItem['price'],
            ]);

            // Find the item
            $item = Item::where('item_code', $grnItem['item_code'])->first();

            if ($item) {
                // Check latest price
                $latestPrice = ItemPrice::where('item_id', $item->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (
                    !$latestPrice ||
                    $latestPrice->rate != $grnItem['rate'] ||
                    $latestPrice->cost_price != $grnItem['cost_price']
                ) {
                    // Insert new price
                    ItemPrice::create([
                        'item_id'    => $item->id,
                        'rate'       => $grnItem['rate'],
                        'cost_price' => $grnItem['cost_price'],
                    ]);

                    // Update item table
                    $item->update([
                        'rate'       => $grnItem['rate'],
                        'cost_price' => $grnItem['cost_price'],
                    ]);
                }

                // Create stock transaction
                StockTransaction::create([
                    'item_code'        => $grnItem['item_code'],
                    'item_name'        => $grnItem['item_name'],
                    'transaction_type' => 'IN',
                    'quantity'         => $grnItem['quantity'],
                    'rate'             => $grnItem['rate'],
                    'cost_price'       => $grnItem['cost_price'],
                    'price'            => $grnItem['price'],
                    'reference_no'     => $master->grn_no,
                    'source'           => 'Supplier GRN',
                    'transaction_date' => $master->g_date,
                ]);
            }
        }
    });

    return redirect()->route('bill.show', $request->grn_no)
                     ->with('success', 'GRN Created Successfully.');
}

    public function show($grn_no)
    {
        $bill = SupplierGRNMaster::with('details')->findOrFail($grn_no);
        return view('bill.show', compact('bill'));
    }

    public function edit($grn_no)
    {
        $bill = SupplierGRNMaster::with('details')->findOrFail($grn_no);
        return view('bill.edit', compact('bill'));
    }

   public function update(Request $request, $grn_no)
{
    $request->validate([
        'g_date' => 'required|date',
        'supplier_name' => 'required|string',
        'total_price' => 'required|numeric',
        'tobe_price' => 'required|numeric',
        'total_discount' => 'required|numeric',
        'supplier_pay' => 'required|numeric',
        'balance' => 'required|numeric',

        'details.*.item_code' => 'required|integer',
        'details.*.item_name' => 'required|string',
        'details.*.rate' => 'required|numeric',
'details.*.cost_price' => 'required|numeric',
        'details.*.quantity' => 'required|integer',
        'details.*.price' => 'required|numeric',
    ]);

    DB::transaction(function () use ($request, $grn_no) {
        // Update master
        $master = SupplierGRNMaster::findOrFail($grn_no);
        $master->update($request->only([
            'g_date', 'supplier_name', 'total_price',
            'tobe_price', 'total_discount', 'supplier_pay', 'balance',
        ]));

        // Delete old details and stock transactions
        SupplierGRNDetails::where('grn_no', $grn_no)->delete();
        StockTransaction::where('reference_no', $grn_no)->where('source', 'Supplier GRN')->delete();

        foreach ($request->details as $item) {
            // Save new details
            SupplierGRNDetails::create(array_merge($item, ['grn_no' => $grn_no]));

            // Add new stock transaction
            StockTransaction::create([
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'transaction_type' => 'IN',
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
    'cost_price' => $item['cost_price'], 
                'price' => $item['price'],
                'reference_no' => $grn_no,
                'source' => 'Supplier GRN',
                'transaction_date' => $request->g_date,
            ]);
        }

       
    });

    return redirect()->route('bill.show', $grn_no)->with('success', 'GRN Updated and Stock Adjusted Successfully.');
}





    public function itemNameSearch(Request $request)
{
    $query = $request->input('query');

    $items = Item::where('item_name', 'LIKE', "%$query%")
                ->limit(10)
                ->get();

    return response()->json($items);
}
public function itemPriceSearch(Request $request)
{
    $query = $request->input('query');

    $rate = ItemPrice::with('item')  // assuming relation is 'item'
                 ->whereRaw("CAST(rate AS CHAR) LIKE ?", ["$query%"])
                 ->limit(10)
                 ->get()
                 ->map(function ($r) {
                     return [
                         'item_code' => $r->item->item_code,
                         'item_name' => $r->item->item_name,
                         'rate'      => $r->item->rate,
                         'cost_price'=>$r->cost_price,
                     ];
                 });

    return response()->json($rate);
}


public function itemCodeSearch(Request $request)
{
    $query = $request->input('query');

    $items = Item::where('item_code', 'LIKE', "%$query%")
                ->limit(10)
                ->get();

    return response()->json($items);
}

public function summary(Request $request)
{
    $from = $request->input('from_date');
    $to = $request->input('to_date');

    $query = SupplierGRNMaster::query();

    if ($from && $to) {
        $query->whereBetween('g_date', [$from, $to]);
    } elseif ($from) {
        $query->whereDate('g_date', '>=', $from);
    } elseif ($to) {
        $query->whereDate('g_date', '<=', $to);
    }

    $bills = $query->orderBy('g_date', 'desc')->get();

    $grouped = $bills->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->g_date)->format('Y-m-d');
    });

    $dailySummaries = $grouped->map(function($group, $date) {
        return [
            'date' => $date,
            'grn_count' => $group->count(),
            'total_price' => $group->sum('total_price'),
            'total_discount' => $group->sum('total_discount'),
            'total_issued' => $group->sum('tobe_price'),
        ];
    });

    // Calculate grand totals
    $totals = [
        'grn_count' => $dailySummaries->sum('grn_count'),
        'total_price' => $dailySummaries->sum('total_price'),
        'total_discount' => $dailySummaries->sum('total_discount'),
        'total_issued' => $dailySummaries->sum('total_issued'),
    ];

    return view('bill.summary', compact('dailySummaries', 'totals'));
}





public function downloadSummaryPdf(Request $request)
{
    $from = $request->input('from_date');
    $to = $request->input('to_date');

    $query = SupplierGRNMaster::query();

    if ($from && $to) {
        $query->whereBetween('g_date', [$from, $to]);
    } elseif ($from) {
        $query->whereDate('g_date', '>=', $from);
    } elseif ($to) {
        $query->whereDate('g_date', '<=', $to);
    }

    $bills = $query->orderBy('g_date', 'desc')->get();

    $grouped = $bills->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->g_date)->format('Y-m-d');
    });

    $dailySummaries = $grouped->map(function($group, $date) {
        return [
            'date' => $date,
            'grn_count' => $group->count(),
            'total_price' => $group->sum('total_price'),
            'total_discount' => $group->sum('total_discount'),
            'total_issued' => $group->sum('tobe_price'),
        ];
    });

    $totals = [
        'grn_count' => $dailySummaries->sum('grn_count'),
        'total_price' => $dailySummaries->sum('total_price'),
        'total_discount' => $dailySummaries->sum('total_discount'),
        'total_issued' => $dailySummaries->sum('total_issued'),
    ];

    $pdf = PDF::loadView('bill.summary_pdf', compact('dailySummaries', 'totals', 'from', 'to'));
    return $pdf->download('GRN_Summary_Report.pdf');
}


    // Detail page for a single GRN
public function grnDetailsByDate($date)
{
    $grns = SupplierGRNMaster::whereDate('g_date', $date)->get();

    $totals = [
        'total_price' => $grns->sum('total_price'),
        'total_discount' => $grns->sum('total_discount'),
        'total_issued' => $grns->sum('tobe_price'),
        'total_paid' => $grns->sum('supplier_pay'),
        'total_balance' => $grns->sum('balance'),
    ];

    return view('bill.details', compact('grns', 'date', 'totals'));
}




public function downloadGrnPdf($date)
{
    $grns = SupplierGRNMaster::whereDate('g_date', $date)->get();

    $totals = [
        'total_price' => $grns->sum('total_price'),
        'total_discount' => $grns->sum('total_discount'),
        'total_issued' => $grns->sum('tobe_price'),
        'total_paid' => $grns->sum('supplier_pay'),
        'total_balance' => $grns->sum('balance'),
    ];

    $pdf = PDF::loadView('bill.details_pdf', compact('grns', 'date', 'totals'));
    return $pdf->download("GRN_Details_$date.pdf");
}











    public function index()
    {
        $bills = SupplierGRNMaster::with('details')->get();
        return view('bill.index', compact('bills'));
    }

    public function gurureport(Request $request)
    {
        $query = SupplierGRNMaster::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('g_date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('g_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('g_date', '<=', $request->to_date);
        }

        $bills = $query->orderBy('g_date', 'desc')->get();

        return view('bill.report', compact('bills'));
    }

    public function guruexportPdf(Request $request)
    {
        $query = SupplierGRNMaster::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('g_date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('g_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('g_date', '<=', $request->to_date);
        }

        $bills = $query->orderBy('g_date', 'desc')->get();

        $totalSales = $bills->sum('total_price');
        $totalDiscount = $bills->sum('total_discount');
        $totalIssued = $bills->sum('tobe_price');

      $grouped = $bills->groupBy(fn ($g) => \Carbon\Carbon::parse($g->g_date)->format('Y-m-d'));


        $dailySummaries = [];
        foreach ($grouped as $date => $set) {
            $dailySummaries[$date] = [
                'totalSales' => $set->sum('total_price'),
                'totalDiscount' => $set->sum('total_discount'),
                'totalIssued' => $set->sum('tobe_price'),
            ];
        }

        $pdf = PDF::loadView('bill.report_pdf', [
            'groupedGrns'    => $grouped,
            'dailySummaries' => $dailySummaries,
            'totalSales'     => $totalSales,
            'totalDiscount'  => $totalDiscount,
            'totalIssued'    => $totalIssued,
            'fromDate'       => $request->from_date,
            'toDate'         => $request->to_date,
        ]);

        return $pdf->download('bill-report.pdf');
    }



     public function search(Request $request)
    {
        $grn_no = $request->input('grn_no');

        $bill = SupplierGRNMaster::with('details')->where('grn_no', $grn_no)->first();

        if (!$bill) {
            return back()->withErrors(['error' => 'Bill No not found']);
        }

        $suppliers = Supplier::all();  
        return view('bill.search_result', compact('bill', 'suppliers'));
    }
}

