<?php


namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPrice;

use App\Models\StockTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }


public function store(Request $request)
{
    $request->validate([
        'item_code'   => 'required|string|unique:item,item_code',
        'item_name'   => 'required|string',
        'rate'        => 'required|integer',
        'unit' => 'required|string|max:20',
  'category' => 'required|string',
        'cost_price'  => 'required|integer',
        'stock'       => 'required|integer',
    ]);

    // Create the item
    $item = Item::create($request->only([
        'item_code', 'item_name', 'rate', 'cost_price', 'stock','unit', 'category'
    ]));

    // Record opening stock as initial transaction
    if ($item->stock > 0) {
        StockTransaction::create([
            'item_code'        => $item->item_code,
            'item_name'        => $item->item_name,
            'transaction_type' => 'IN',
            'quantity'         => $item->stock,
            'rate'             => $item->rate,
            'unit'             => $item->unit,
            'category'             => $item->category,
            'price'            => $item->stock * $item->rate,
            'reference_no'     => 'OPENING-STOCK-' . now()->format('YmdHis'),
            'source'           => 'Opening Balance',
            'transaction_date' => Carbon::now()->toDateString(),
            'is_opening'       => true,
        ]);



            $item->prices()->create([
        'rate'       => $request->rate,
        'cost_price' => $request->cost_price,
    ]);
    }

    return redirect()->route('items.index')->with('success', 'Item created with opening stock.');
}



    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

public function update(Request $request, Item $item)
{
    $request->validate([
        'item_code'   => 'required|string|unique:item,item_code,' . $item->id,
        'item_name'   => 'required|string',
        'unit'        => 'required|string|max:20',
        'category'    => 'required|string',
        'rate'        => 'required|numeric',
        'cost_price'  => 'required|numeric',
    ]);

    $item->update([
        'item_code'  => $request->item_code,
        'item_name'  => $request->item_name,
        'unit'       => $request->unit,
        'category'   => $request->category,
        'rate'       => $request->rate,
        'cost_price' => $request->cost_price,
    ]);

    $lastPrice = $item->prices()->latest('created_at')->first();

    if (
        !$lastPrice || 
        $lastPrice->rate != $request->rate || 
        $lastPrice->cost_price != $request->cost_price
    ) {
        $item->prices()->create([
            'rate'       => $request->rate,
            'cost_price' => $request->cost_price,
        ]);
    }

    return redirect()->route('items.index')->with('success', 'Item updated successfully.');
}


    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

public function stock(Request $request)
{
    $query = Item::query();

    
    if ($request->has('date') && $request->date) {
        $query->whereDate('created_at', $request->date);
    }

    $items = $query->get();

    foreach ($items as $item) {
        if ($item->stock <= 0) {
            $item->status = 'Out of Stock';
        } elseif ($item->stock <= 50) {
            $item->status = 'Low Stock';
        } else {
            $item->status = 'Available';
        }
    }

    $totalStock = $items->sum('stock');

    return view('items.stock', compact('items', 'totalStock'));
}




}
