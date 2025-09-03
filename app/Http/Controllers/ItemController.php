<?php


namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPrice;
use Illuminate\Validation\Rule;

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

public function checkCode(Request $request)
{
    $exists = Item::where('item_code', $request->item_code)->exists();

    return response()->json(['exists' => $exists]);
}

public function store(Request $request)
{
$request->validate([
    'item_code' => [
        'required',
        'string',
        Rule::unique('item', 'item_code'),
    ],
    'item_name' => [
        'required',
        'string',
        Rule::unique('item')->where(function ($query) use ($request) {
            return $query->whereRaw('LOWER(item_name) = ?', [strtolower($request->item_name)]);
        }),
    ],
    'rate'       => 'required|numeric|min:0',
    'cost_price' => 'required|numeric|min:0',
    'stock'      => 'required|integer|min:0',
    'unit'       => 'required|string|max:20',
    'category'   => 'required|string|max:100',
    'discount_1'      => 'nullable|numeric|min:0',
    'discount_2'      => 'nullable|numeric|min:0',
    'discount_3'      => 'nullable|numeric|min:0',
    'discount_1_qty'  => 'nullable|integer|min:0',
    'discount_2_qty'  => 'nullable|integer|min:0',
    'discount_3_qty'  => 'nullable|integer|min:0',
], [
    'item_name.unique' => '❗ Item name already exists (case-insensitive). Please choose a different name.',
]);




    $item = Item::create($request->only([
        'item_code', 'item_name', 'rate', 'cost_price', 'stock', 'unit', 'category',
        'discount_1', 'discount_2', 'discount_3',
        'discount_1_qty', 'discount_2_qty', 'discount_3_qty'
    ]));

    // Rest of your logic for StockTransaction and ItemPrice here...
    if ($item->stock > 0) {
        StockTransaction::create([
            'item_code'        => $item->item_code,
            'item_name'        => $item->item_name,
            'transaction_type' => 'IN',
            'quantity'         => $item->stock,
            'rate'             => $item->rate,
            'unit'             => $item->unit,
            'category'         => $item->category,
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
public function checkName(Request $request)
{
    $exists = Item::where('item_name', $request->item_name)->exists();

    return response()->json(['exists' => $exists]);
}

public function update(Request $request, Item $item)
{
$request->validate([
    'item_code' => [
        'required',
        'string',
        Rule::unique('item', 'item_code')->ignore($item->id),
    ],
    'item_name' => [
        'required',
        'string',
        Rule::unique('item')->ignore($item->id)->where(function ($query) use ($request) {
            return $query->whereRaw('LOWER(item_name) = ?', [strtolower($request->item_name)]);
        }),
    ],
        'unit'         => 'required|string|max:20',
        'category'     => 'required|string',
        'rate'         => 'required|numeric',
        'cost_price'   => 'required|numeric',
        'discount_1'   => 'nullable|integer|min:0|max:100',
        'discount_2'   => 'nullable|integer|min:0|max:100',
        'discount_3'   => 'nullable|integer|min:0|max:100',
        'discount_1_qty' => 'nullable|integer|min:0',
        'discount_2_qty' => 'nullable|integer|min:0',
        'discount_3_qty' => 'nullable|integer|min:0',
],
 [
    'item_name.unique' => '❗ Item name already exists (case-insensitive). Please choose a different name.',
]);
    $item->update([
        'item_code'     => $request->item_code,
        'item_name'     => $request->item_name,
        'rate'          => $request->rate,
        'cost_price'    => $request->cost_price,
        'stock'         => $request->stock,
        'unit'          => $request->unit,
        'category'      => $request->category,
        'discount_1'    => $request->discount_1,
        'discount_2'    => $request->discount_2,
        'discount_3'    => $request->discount_3,
        'discount_1_qty' => $request->discount_1_qty,
        'discount_2_qty' => $request->discount_2_qty,
        'discount_3_qty' => $request->discount_3_qty,
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
