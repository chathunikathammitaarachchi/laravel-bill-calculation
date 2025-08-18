<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;

class StockHistoryController extends Controller
{
   public function index(Request $request)
{
    $start = $request->input('start_date');
    $end   = $request->input('end_date');

    $transactions = StockTransaction::when($start && $end, function ($q) use ($start, $end) {
            return $q->whereBetween('transaction_date', [$start, $end]);
        })
        ->orderBy('transaction_date')
        ->orderBy('id')
        ->get();

    $grouped = [];
    $balance = [];

    foreach ($transactions as $tx) {
        $code = $tx->item_code;
        $balance[$code] = $balance[$code] ?? 0;

        $qty = $tx->transaction_type === 'GRN' ? $tx->quantity : -$tx->quantity;
        $balance[$code] += $qty;

        $grouped[$code][] = [
            'date'    => $tx->transaction_date,
            'name'    => $tx->item_name,
            'type'    => $tx->transaction_type,
            'qty'     => $qty,
            'balance' => $balance[$code],
        ];
    }

    return view('stock_history.ledger', [
        'groupedHistory' => $grouped,
        'start' => $start,
        'end' => $end,
    ]);
}

}
