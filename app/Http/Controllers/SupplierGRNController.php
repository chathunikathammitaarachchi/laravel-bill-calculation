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

use App\Models\Supplier;


class SupplierGRNController extends Controller
{
    public function create()
    {
        $last = SupplierGRNMaster::orderBy('grn_no', 'desc')->first();
        $nextGrnNo = $last ? intval($last->grn_no) + 1 : 1;



          $suppliers = Supplier::all(); 

    $items = Item::all(); 
   $rates = ItemPrice::all(); 
$items = \App\Models\Item::with('itemPrices')->get();


    


        return view('bill.create', [
            'nextGrnNo' => $nextGrnNo,
            'suppliers' => $suppliers,
        'items'     => $items,
        'rates' => $rates
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_no' => 'required|unique:grnmaster,grn_no',
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
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric',
        ]);



        
        DB::transaction(function () use ($request) {
            $master = SupplierGRNMaster::create($request->only([
                'grn_no','g_date','supplier_name','total_price',
                'tobe_price','total_discount','supplier_pay','balance',
                
            ]));

            foreach ($request->items as $item) {
                SupplierGRNDetails::create(array_merge($item, ['grn_no' => $master->grn_no]));
            }



foreach ($request->items as $grnItem) {
  

        StockTransaction::create([
            'item_code' => $grnItem['item_code'],
            'item_name' => $grnItem['item_name'],
            'transaction_type' => 'IN',
            'quantity' => $grnItem['quantity'],
            'rate' => $grnItem['rate'],
            'price' => $grnItem['price'],
            'reference_no' => $request->grn_no,
            'source' => 'Supplier GRN',
            'transaction_date' => $request->g_date,
        ]);
    
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
'details.*.quantity' => 'required|integer',
'details.*.price' => 'required|numeric',

        ]);

        DB::transaction(function () use ($request, $grn_no) {
            $master = SupplierGRNMaster::findOrFail($grn_no);
            $master->update($request->only([
                'g_date','supplier_name','total_price',
                'tobe_price','total_discount','supplier_pay','balance',
                
            ]));


       

            SupplierGRNDetails::where('grn_no', $grn_no)->delete();

            foreach ($request->details as $item) {
    $rate = ItemPrice::where('item_id', $item['item_code'])->value('rate');

                SupplierGRNDetails::create(array_merge($item, ['grn_no' => $grn_no]));
            }
        });

        return redirect()->route('bill.show', $grn_no)->with('success', 'GRN Updated Successfully.');
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
                         'rate'      => $r->rate,
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

