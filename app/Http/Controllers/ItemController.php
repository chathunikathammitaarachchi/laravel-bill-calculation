<?php


namespace App\Http\Controllers;

use App\Models\Item;
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
            'item_code' => 'required|integer|unique:item,item_code',
            'item_name' => 'required|string',
            'rate' => 'required|integer',
            'stock' => 'required|integer',

        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Item added successfully.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'item_code' => 'required|integer|unique:item,item_code,' . $item->id,
            'item_name' => 'required|string',
            'rate' => 'required|integer',
            'stock' => 'required|integer',

        ]);

        $item->update($request->all());

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
