<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Item;
use App\Models\GRNMaster;
use App\Models\GRNDetails;
use App\Models\ItemSummary;

use Illuminate\Support\Facades\DB;
use PDF; 
use App\Models\StockTransaction;

class GRNController extends Controller
{
   public function create()
{



    $lastGrn = GRNMaster::orderBy('id', 'desc')->first(); 
    $nextBillNo = $lastGrn ? intval($lastGrn->bill_no) + 1 : 1; 



    $customers = Customer::all();
    $items = Item::all();




    


    
    return view('grn.create', compact('customers', 'items', 'nextBillNo'));
}


  public function store(Request $request)
{
    $request->validate([
        'bill_no' => 'required|unique:bill_master,bill_no',
        'grn_date' => 'required|date',
        'customer_name' => 'required|string',
        'total_price' => 'required|numeric',
        'tobe_price' => 'required|numeric',
        'total_discount' => 'required|numeric',
        'customer_pay' => 'required|numeric',
        'balance' => 'required|numeric',
        'received_by' => 'required|string',
        'issued_by' => 'required|string',
        'items.*.item_code' => 'required|integer',
        'items.*.item_name' => 'required|string',
        'items.*.rate' => 'required|numeric',
        'items.*.quantity' => 'required|integer',
        'items.*.price' => 'required|numeric',
    ]);

    $grn = DB::transaction(function () use ($request) {
        $grn = GRNMaster::create([
            'bill_no' => $request->bill_no,
            'grn_date' => $request->grn_date,
            'customer_name' => $request->customer_name,
            'total_price' => $request->total_price,
            'tobe_price' => $request->tobe_price,
            'total_discount' => $request->total_discount,
            'received_by' => $request->received_by,
            'issued_by' => $request->issued_by,
            'customer_pay' => $request->customer_pay,
            'balance' => $request->balance,
        ]);

        foreach ($request->items as $item) {
            GRNDetails::create([
                'bill_no' => $grn->bill_no,
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'rate' => $item['rate'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);


            $grnDate = \App\Models\GRNMaster::where('bill_no', $request->bill_no)->value('grn_date');

            // Save into summary table
            ItemSummary::create([
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'total_price' => $item['price'],
                'bill_no' => $grn->bill_no,
                'grn_date' => $request->grn_date,
            ]);
        }

        foreach ($request->items as $billItem) {
            $item = Item::where('item_code', $billItem['item_code'])->first();

            if ($item && $item->stock >= $billItem['quantity']) {
                $item->stock -= $billItem['quantity'];
                $item->save();

                StockTransaction::create([
                    'item_code' => $billItem['item_code'],
                    'item_name' => $billItem['item_name'],
                    'transaction_type' => 'OUT',
                    'quantity' => $billItem['quantity'],
                    'rate' => $billItem['rate'],
                    'price' => $billItem['price'],
                    'reference_no' => $request->bill_no,
                    'source' => 'Customer Bill',
                    'transaction_date' => $request->grn_date,
                ]);
            } else {
                return back()->with('error', 'Not enough stock for item: ' . $item->item_name);
            }
        }

        return $grn;
    });

    return redirect()->route('grn.show', $grn->bill_no)->with('success', 'GRN Created Successfully.');
}




    public function show($bill_no)
    {
        $grn = GRNMaster::with('details')->where('bill_no', $bill_no)->firstOrFail();
        $customers = Customer::all(); 
        return view('grn.show', compact('grn', 'customers'));
    }

    public function search(Request $request)
    {
        $bill_no = $request->input('bill_no');

        $grn = GRNMaster::with('details')->where('bill_no', $bill_no)->first();

        if (!$grn) {
            return back()->withErrors(['error' => 'Bill No not found']);
        }

        $customers = Customer::all();  
        return view('grn.search_result', compact('grn', 'customers'));
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

    $items = Item::where('rate', 'LIKE', "$query%")
                ->limit(10)
                ->get();

    return response()->json($items);
}

public function itemCodeSearch(Request $request)
{
    $query = $request->input('query');

    $items = Item::where('item_code', 'LIKE', "%$query%")
                ->limit(10)
                ->get();

    return response()->json($items);
}

    public function edit($bill_no)
    {
        $grn = GRNMaster::with('details')->where('bill_no', $bill_no)->firstOrFail();
        $customers = Customer::all();  
        return view('grn.edit', compact('grn', 'customers'));
    }

 public function update(Request $request, $bill_no)
{

    $bill = Bill::where('grn_no', $grn_no)->with('details')->firstOrFail();

    

    
    $validated = $request->validate([
        'bill_no' => 'required|unique:bill_master,bill_no,' . $bill_no . ',bill_no',
        'grn_date' => 'required|date',
        'customer_name' => 'required|string',
        'total_price' => 'required|numeric',
        'tobe_price' => 'required|numeric',
        'total_discount' => 'required|numeric',
        'customer_pay' => 'required|numeric',
        'balance' => 'required|numeric',
        'received_by' => 'required|string',
        'issued_by' => 'required|string',
        'details.*.item_code' => 'required|integer',
        'details.*.item_name' => 'required|string',
        'details.*.rate' => 'required|numeric',
        'details.*.quantity' => 'required|integer',
        'details.*.price' => 'required|numeric',
    ]);

    DB::beginTransaction();

    try {
        $grnMaster = GRNMaster::where('bill_no', $bill_no)->firstOrFail();

        $grnMaster->update([
            'grn_date' => $validated['grn_date'],
            'customer_name' => $validated['customer_name'],
            'total_price' => $validated['total_price'],
            'total_discount' => $validated['total_discount'] ?? 0,
            'received_by' => $validated['received_by'],
            'issued_by' => $validated['issued_by'],
            'tobe_price' => $validated['tobe_price'],
            'customer_pay' => $validated['customer_pay'],
            'balance' => $validated['balance'],
        ]);

        
        GRNDetails::where('bill_no', $bill_no)->delete();

        foreach ($validated['details'] as $detail) {
            GRNDetails::create([
                'bill_no' => $bill_no,
                'item_code' => $detail['item_code'],
                'item_name' => $detail['item_name'],
                'rate' => $detail['rate'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
            ]);
        }

        DB::commit();

        return redirect()->route('grn.show', $bill_no)->with('success', 'Bill updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();

        return back()->withErrors(['error' => 'Failed to update bill: ' . $e->getMessage()])->withInput();
    }
}

    public function index()
    {
        $grns = GRNMaster::with('details')->get();
        return view('grn.index', compact('grns'));
    }










public function report(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    $query = GRNMaster::query();

    if ($fromDate && $toDate) {
        $query->whereBetween('grn_date', [$fromDate, $toDate]);
    } elseif ($fromDate) {
        $query->whereDate('grn_date', '>=', $fromDate);
    } elseif ($toDate) {
        $query->whereDate('grn_date', '<=', $toDate);
    }

    $grns = $query->orderBy('grn_date', 'desc')->get();

    return view('grn.report', compact('grns', 'fromDate', 'toDate'));
}


public function exportPdf(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    $query = GRNMaster::query();

    if ($fromDate && $toDate) {
        $query->whereBetween('grn_date', [$fromDate, $toDate]);
    } elseif ($fromDate) {
        $query->whereDate('grn_date', '>=', $fromDate);
    } elseif ($toDate) {
        $query->whereDate('grn_date', '<=', $toDate);
    }

    $grns = $query->orderBy('grn_date', 'desc')->get();

    
    $totalSales = $grns->sum('total_price');
    $totalDiscount = $grns->sum('total_discount');
    $totalIssued = $grns->sum('tobe_price');

    
    $groupedGrns = $grns->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->grn_date)->format('Y-m-d');
    });

    
    $dailySummaries = [];
    foreach ($groupedGrns as $date => $dailyGrns) {
        $dailySummaries[$date] = [
            'totalSales' => $dailyGrns->sum('total_price'),
            'totalDiscount' => $dailyGrns->sum('total_discount'),
            'totalIssued' => $dailyGrns->sum('tobe_price'),
        ];
    }

    
    $pdf = PDF::loadView('grn.report_pdf', [
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        'groupedGrns' => $groupedGrns,
        'dailySummaries' => $dailySummaries,
        'totalSales' => $totalSales,
        'totalDiscount' => $totalDiscount,
        'totalIssued' => $totalIssued,
    ]);

    return $pdf->download('grn-report.pdf');
}



}
