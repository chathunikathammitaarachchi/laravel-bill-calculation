<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Item;
use App\Models\GRNMaster;
use App\Models\GRNDetails;
use App\Models\ItemSummary;
use App\Models\CustomerDue;
use App\Models\DuePayment;
use App\Models\ItemPrice;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockTransaction;

class GRNController extends Controller
{
   public function create()
{
    $lastGrn = GRNMaster::orderBy('id', 'desc')->first(); 
    $nextBillNo = $lastGrn ? intval($lastGrn->bill_no) + 1 : 1; 

    $customers = Customer::all();
    $items = Item::all();
    $rates = ItemPrice::all(); 
$items = \App\Models\Item::with('itemPrices')->get();


    return view('grn.create', compact('customers', 'items', 'nextBillNo','rates'));
    }


  public function store(Request $request)
  {
    $request->validate([
        'bill_no' => 'required|unique:bill_master,bill_no',
        'grn_date' => 'required|date',
        'customer_name' => 'nullable|string',
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

    $customerName = $request->customer_name;

// If customer_name is empty, assign "Cash" as default
if (empty($customerName)) {
    $customerName = 'Cash';
    
    // Check if "Cash" customer already exists
    $cashCustomer = Customer::where('customer_name', 'Cash')->first();

    // If not, create it
    if (!$cashCustomer) {
        Customer::create([
            'customer_name' => 'Cash',
        ]);
    }
   }
if ($customerName === 'Cash' && $request->customer_pay < $request->tobe_price) {
    return redirect()->back()->with('cash_due_error', 'Cash customer cannot have due. Please ensure full payment is made.');
}


    $grn = DB::transaction(function () use ($request, $customerName) {

        $grn = GRNMaster::create([
            'bill_no' => $request->bill_no,
            'grn_date' => $request->grn_date,
            'customer_name' => $customerName,
            'total_price' => $request->total_price,
            'tobe_price' => $request->tobe_price,
            'total_discount' => $request->total_discount,
            'received_by' => $request->received_by,
            'issued_by' => $request->issued_by,
            'customer_pay' => $request->customer_pay,
            'balance' => $request->balance,
            
        ]);

// 1. UPDATE PHP CODE (Controller store method)

foreach ($request->items as $itemData) {
    $item = Item::where('item_code', $itemData['item_code'])->first();

    if (!$item) {
        return back()->with('error', 'Item not found: ' . $itemData['item_name']);
    }

    $rate = $itemData['rate']; 
   $discount = 0;
$quantity = $itemData['quantity'];

// Apply the highest applicable discount
if ($item->discount_3_qty !== null && $quantity >= $item->discount_3_qty) {
    $discount = $item->discount_3;
} elseif ($item->discount_2_qty !== null && $quantity >= $item->discount_2_qty) {
    $discount = $item->discount_2;
} elseif ($item->discount_1_qty !== null && $quantity >= $item->discount_1_qty) {
    $discount = $item->discount_1;
}


    // CHANGED: Direct subtraction instead of percentage calculation
    $basePrice = $rate * $quantity;
    $finalPrice = $basePrice - $discount; // Direct subtraction of fixed amount

    // Ensure price doesn't go negative
    $finalPrice = max($finalPrice, 0);

    GRNDetails::create([
        'bill_no'   => $grn->bill_no,
        'item_code' => $itemData['item_code'],
        'item_name' => $itemData['item_name'],
        'rate'      => $rate,
        'quantity'  => $quantity,
        'price'     => $finalPrice,
    ]);

    ItemSummary::create([
        'item_code' => $itemData['item_code'],
        'item_name' => $itemData['item_name'],
        'quantity'  => $quantity,
        'rate'      => $rate,
        'total_price' => $finalPrice,
        'bill_no'   => $grn->bill_no,
        'grn_date'  => $request->grn_date,
    ]);
}



// customer due  transaction:

if ($request->customer_pay < $request->tobe_price) {
    CustomerDue::create([
        'customer_name' => $customerName,
        'bill_no' => $request->bill_no,
        'grn_date' => $request->grn_date,
        'tobe_price' => $request->tobe_price,
        'customer_pay' => $request->customer_pay,
        'balance' => $request->tobe_price - $request->customer_pay, 
    ]);
}



        foreach ($request->items as $billItem) {
            $item = Item::where('item_code', $billItem['item_code'])->first();

           if ($item && $item->stock >= $billItem['quantity']) {
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


public function showDues(Request $request)
{
    $dues = CustomerDue::select(
        'customer_name',

        DB::raw('SUM(tobe_price) as total_due'),
        DB::raw('SUM(customer_pay) as total_paid'),
        DB::raw('SUM(balance) as total_balance'),
        DB::raw('MAX(grn_date) as last_date') 
    )
    ->when($request->from_date, fn($q) => $q->whereDate('grn_date', '>=', $request->from_date))
    ->when($request->to_date, fn($q) => $q->whereDate('grn_date', '<=', $request->to_date))
    ->when($request->customer_name, fn($q) => $q->where('customer_name', $request->customer_name))
    ->groupBy('customer_name')
    ->orderBy('customer_name')
   
    ->get();

    return view('grn.dues', compact('dues'));
}

public function customerexportDuesPDF(Request $request)
{
    $query = CustomerDue::query();

    if ($request->filled('from_date')) {
        $query->whereDate('grn_date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('grn_date', '<=', $request->to_date);
    }

    if ($request->filled('customer_name')) {
        $query->where('customer_name', 'like', $request->customer_name . '%');
    }

    $dues = $query->orderBy('grn_date', 'desc')->get();

    $pdf = Pdf::loadView('grn.dues_pdf', compact('dues'));
    return $pdf->stream('customer_dues.pdf');
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

    $items = Item::where('item_name', 'ILIKE', "%$query%")
                ->limit(10)
                ->get();

    return response()->json($items);
}

public function itemPriceSearch(Request $request)
{
    $query = $request->input('query');

    $rate = ItemPrice::with('item')  // assuming relation is 'item'
                 ->whereRaw("CAST(rate AS CHAR) ILIKE ?", ["$query%"])
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

    $items = Item::where('item_code', 'ILIKE', "%$query%")
                ->limit(10)
                ->get();

    return response()->json($items);
}

    public function edit($bill_no)
    {
        $grn = GRNMaster::with('details')->where('bill_no', $bill_no)->firstOrFail();
        $customers = Customer::all();  
            $items = Item::all(); 

        return view('grn.edit', compact('grn', 'customers','items'));
    }

public function update(Request $request, $bill_no)
{
    $bill = GRNMaster::where('bill_no', $bill_no)->with('details')->firstOrFail();

    $validated = $request->validate([
        'grn_date' => 'required|date',
        'customer_name' => 'required|string',
        'total_price' => 'required|numeric',
        'tobe_price' => 'required|numeric',
        'total_discount' => 'required|numeric',
        'customer_pay' => 'required|numeric',
        'balance' => 'required|numeric',
        'received_by' => 'required|string',
        'issued_by' => 'required|string',
        'details.*.item_code' => 'required', 
        'details.*.item_name' => 'required|string',
        'details.*.rate' => 'required|numeric',
        'details.*.quantity' => 'required|integer|min:1',
        'details.*.price' => 'required|numeric',
    ]);

 
    DB::beginTransaction();

    try {
        
       

        // Delete old details & stock transactions
        GRNDetails::where('bill_no', $bill_no)->delete();
        StockTransaction::where('reference_no', $bill_no)
            ->where('transaction_type', 'OUT')
            ->where('source', 'Customer Bill')
            ->delete();

        // **Delete old ItemSummary entries related to this GRN**
        ItemSummary::where('bill_no', $bill_no)->delete();

        // Update master
        $bill->update([
            'grn_date' => $validated['grn_date'],
            'customer_name' => $validated['customer_name'],
            'total_price' => $validated['total_price'],
            'tobe_price' => $validated['tobe_price'],
            'total_discount' => $validated['total_discount'],
            'customer_pay' => $validated['customer_pay'],
            'balance' => $validated['balance'],
            'received_by' => $validated['received_by'],
            'issued_by' => $validated['issued_by'],
        ]);

        // Loop new details
        foreach ($validated['details'] as $detail) {
            $item = Item::where('item_code', $detail['item_code'])->first();
            if (!$item) {
                DB::rollBack();
                return back()->with('error', 'Item not found: ' . $detail['item_name']);
            }

            if ($item->stock < $detail['quantity']) {
                DB::rollBack();
                return back()->with('error', 'Insufficient stock for item: ' . $detail['item_name']);
            }

            

            // Create GRN detail
            GRNDetails::create([
                'bill_no' => $bill_no,
                'item_code' => $detail['item_code'],
                'item_name' => $detail['item_name'],
                'rate' => $detail['rate'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
            ]); 



$adjustedQty = abs($detail['quantity']);

            // Stock transaction
            StockTransaction::create([
                'item_code' => $detail['item_code'],
                'item_name' => $detail['item_name'],
                'transaction_type' => 'OUT',
                'quantity' => $detail['quantity'],
                'rate' => $detail['rate'],
                'price' => $detail['price'],
                'reference_no' => $bill_no,
                'source' => 'Customer Bill',
                'transaction_date' => $validated['grn_date'],
            ]);

            // **Create or update ItemSummary for each item**
            ItemSummary::create([
                'item_code' => $detail['item_code'],
                'item_name' => $detail['item_name'],
                'quantity' => $detail['quantity'],
                'rate' => $detail['rate'],
                'total_price' => $detail['price'],
                'bill_no' => $bill_no,
                'grn_date' => $validated['grn_date'],
            ]);
        }

        // Create/Update Customer Due
        CustomerDue::updateOrCreate(
            ['bill_no' => $bill_no],
            [
                'customer_name' => $validated['customer_name'],
                'grn_date' => $validated['grn_date'],
                'tobe_price' => $validated['tobe_price'],
                'customer_pay' => $validated['customer_pay'],
                'balance' => $validated['tobe_price'] - $validated['customer_pay'],
            ]
        );

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



    //reports

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

    return $pdf->stream('grn-report.pdf');
}

public function summaryReport(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $customerName = $request->input('customer_name');

    $query = GRNMaster::query();

    // Date Range Filtering
    if ($fromDate && $toDate) {
        $query->whereBetween('grn_date', [$fromDate, $toDate]);
    } elseif ($fromDate) {
        $query->whereDate('grn_date', '>=', $fromDate);
    } elseif ($toDate) {
        $query->whereDate('grn_date', '<=', $toDate);
    }

    if ($customerName) {
        $query->where('customer_name', 'like', "%{$customerName}%");
    }

    $grns = $query->orderBy('grn_date', 'asc')->get();

   // 🔸 Opening Balance Calculation
$openingBalance = 0;
if ($fromDate && $customerName) {
    $openingBalance = GRNMaster::where('customer_name', 'like', "%{$customerName}%")
                        ->whereDate('grn_date', '<', $fromDate)
                        ->sum('tobe_price');
}


$totalTotal = $grns->sum('total_price');
$totalDiscount = $grns->sum('total_discount');
$totalToBePaid = $grns->sum('tobe_price');
$totalPaid = $grns->sum('customer_pay');
$totalBalance = $grns->sum('balance');


return view('grn.summary', compact(
    'grns', 'fromDate', 'toDate', 'customerName', 'openingBalance',
    'totalTotal', 'totalDiscount', 'totalToBePaid', 'totalPaid', 'totalBalance'
));
}



public function summaryReportPDF(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $customerName = $request->input('customer_name');

    $query = GRNMaster::query();

    // Date Range Filtering
    if ($fromDate && $toDate) {
        $query->whereBetween('grn_date', [$fromDate, $toDate]);
    } elseif ($fromDate) {
        $query->whereDate('grn_date', '>=', $fromDate);
    } elseif ($toDate) {
        $query->whereDate('grn_date', '<=', $toDate);
    }

    if ($customerName) {
        $query->where('customer_name', 'like', "%{$customerName}%");
    }

    $grns = $query->orderBy('grn_date', 'asc')->get();

    // Opening Balance Calculation
    $openingBalance = 0;
    if ($fromDate && $customerName) {
        $openingBalance = GRNMaster::where('customer_name', 'like', "%{$customerName}%")
            ->whereDate('grn_date', '<', $fromDate)
            ->sum('tobe_price');
    }

    $totalTotal = $grns->sum('total_price');
    $totalDiscount = $grns->sum('total_discount');
    $totalToBePaid = $grns->sum('tobe_price');
    $totalPaid = $grns->sum('customer_pay');
    $totalBalance = $grns->sum('balance');

    // Group by date for better display in PDF
    $groupedGrns = $grns->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->grn_date)->format('Y-m-d');
    });

    return Pdf::loadView('grn.summary_pdf', compact(
        'groupedGrns', 'fromDate', 'toDate', 'customerName', 'openingBalance',
        'totalTotal', 'totalDiscount', 'totalToBePaid', 'totalPaid', 'totalBalance'
    ))->setPaper('a4')->stream('bill-report.pdf');
}


}
