<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;

class StockHistoryController extends Controller
{
    // public function index(Request $request)
    // {
    //     $start = $request->input('start_date');
    //     $end = $request->input('end_date');
    //     $itemCode = $request->input('item_code');
    //     $itemName = $request->input('item_name');

    //     $grouped = [];
    //     $balance = [];
    //     $inTotal = [];
    //     $outTotal = [];
    //     $openingInBalance = [];
    //     $openingOutBalance = [];

    //     if ($start) {
    //         $openingTransactions = StockTransaction::where('transaction_date', '<', $start);

    //         if ($itemCode) {
    //             $openingTransactions->where('item_code', 'like', "%$itemCode%");
    //         }
    //         if ($itemName) {
    //             $openingTransactions->where('item_name', 'like', "%$itemName%");
    //         }

    //         $openingTransactions = $openingTransactions->get();

    //         foreach ($openingTransactions as $tx) {
    //             $code = $tx->item_code;

    //             $openingInBalance[$code] = $openingInBalance[$code] ?? 0;
    //             $openingOutBalance[$code] = $openingOutBalance[$code] ?? 0;

    //             if ($tx->transaction_type === 'IN') {
    //                 $openingInBalance[$code] += $tx->quantity;
    //             } else {
    //                 $openingOutBalance[$code] += $tx->quantity;
    //             }
    //         }
    //     }

    //     $transactions = StockTransaction::query();

    //     if ($start && $end) {
    //         $transactions->whereBetween('transaction_date', [$start, $end]);
    //     }

    //     if ($itemCode) {
    //         $transactions->where('item_code', 'like', "%$itemCode%");
    //     }

    //     if ($itemName) {
    //         $transactions->where('item_name', 'like', "%$itemName%");
    //     }

    //     $transactions = $transactions->orderBy('transaction_date')
    //         ->orderBy('id')
    //         ->get();

    //     $flatHistory = [];
    //     foreach ($transactions as $tx) {
    //         $code = $tx->item_code;

    //         $balance[$code] = $balance[$code] ?? (
    //             ($openingInBalance[$code] ?? 0) - ($openingOutBalance[$code] ?? 0)
    //         );
    //         $inTotal[$code] = $inTotal[$code] ?? 0;
    //         $outTotal[$code] = $outTotal[$code] ?? 0;

    //         if ($tx->transaction_type === 'IN') {
    //             $qty = $tx->quantity;
    //             $inTotal[$code] += $qty;
    //         } else {
    //             $qty = -$tx->quantity;
    //             $outTotal[$code] += abs($qty);
    //         }

    //         $balance[$code] += $qty;

    //         $flatHistory[] = [
    //             'date' => $tx->transaction_date,
    //             'code' => $tx->item_code,
    //             'name' => $tx->item_name,
    //             'type' => $tx->transaction_type,
    //             'qty' => $qty,
    //             'balance' => $balance[$code],
    //         ];
    //     }


    //     return view('stock.ledger', [
    //         'flatHistory' => $flatHistory,
    //         'start' => $start,
    //         'end' => $end,
    //         'inTotal' => $inTotal,
    //         'outTotal' => $outTotal,
    //         'openingInBalance' => $openingInBalance,
    //         'openingOutBalance' => $openingOutBalance,
    //         'itemCode' => $itemCode,
    //         'itemName' => $itemName,
    //     ]);
    // }

    // public function stokindownloadPdf(Request $request)
    // {
    //     $start = $request->input('start_date');
    //     $end = $request->input('end_date');
    //     $itemCode = $request->input('item_code');
    //     $itemName = $request->input('item_name');

    //     $grouped = [];
    //     $balance = [];
    //     $inTotal = [];
    //     $outTotal = [];
    //     $openingInBalance = [];
    //     $openingOutBalance = [];

    //     if ($start) {
    //         $openingTransactions = StockTransaction::where('transaction_date', '<', $start);

    //         if ($itemCode) {
    //             $openingTransactions->where('item_code', 'like', "%$itemCode%");
    //         }
    //         if ($itemName) {
    //             $openingTransactions->where('item_name', 'like', "%$itemName%");
    //         }

    //         $openingTransactions = $openingTransactions->get();

    //         foreach ($openingTransactions as $tx) {
    //             $code = $tx->item_code;

    //             $openingInBalance[$code] = $openingInBalance[$code] ?? 0;
    //             $openingOutBalance[$code] = $openingOutBalance[$code] ?? 0;

    //             if ($tx->transaction_type === 'IN') {
    //                 $openingInBalance[$code] += $tx->quantity;
    //             } else {
    //                 $openingOutBalance[$code] += $tx->quantity;
    //             }
    //         }
    //     }

    //     $transactions = StockTransaction::query();

    //     if ($start && $end) {
    //         $transactions->whereBetween('transaction_date', [$start, $end]);
    //     }

    //     if ($itemCode) {
    //         $transactions->where('item_code', 'like', "%$itemCode%");
    //     }

    //     if ($itemName) {
    //         $transactions->where('item_name', 'like', "%$itemName%");
    //     }

    //     $transactions = $transactions->orderBy('transaction_date')
    //         ->orderBy('id')
    //         ->get();

    //     foreach ($transactions as $tx) {
    //         $code = $tx->item_code;

    //         $balance[$code] = $balance[$code] ?? (
    //             ($openingInBalance[$code] ?? 0) - ($openingOutBalance[$code] ?? 0)
    //         );
    //         $inTotal[$code] = $inTotal[$code] ?? 0;
    //         $outTotal[$code] = $outTotal[$code] ?? 0;

    //         if ($tx->transaction_type === 'IN') {
    //             $qty = $tx->quantity;
    //             $inTotal[$code] += $qty;
    //         } else {
    //             $qty = -$tx->quantity;
    //             $outTotal[$code] += abs($qty);
    //         }

    //         $balance[$code] += $qty;

    //         $grouped[$code][] = [
    //             'date'    => $tx->transaction_date,
    //             'name'    => $tx->item_name,
    //             'type'    => $tx->transaction_type,
    //             'qty'     => $qty,
    //             'balance' => $balance[$code],
    //         ];
    //     }

    //     $pdf = Pdf::loadView('stock.pdf', [
    //         'groupedHistory' => $grouped,
    //         'start' => $start,
    //         'end' => $end,
    //         'inTotal' => $inTotal,
    //         'outTotal' => $outTotal,
    //         'openingInBalance' => $openingInBalance,
    //         'openingOutBalance' => $openingOutBalance,
    //         'itemCode' => $itemCode,
    //         'itemName' => $itemName,
    //     ]);

    //     return $pdf->download('stock.pdf');
    // }

    // public function autocomplete(Request $request)
    // {
    //     $term = $request->get('term');

    //     $items = \App\Models\Item::query()
    //         ->where('item_code', 'LIKE', "%{$term}%")
    //         ->orWhere('item_name', 'LIKE', "%{$term}%")
    //         ->select('item_code', 'item_name')
    //         ->limit(10)
    //         ->get();


    //     $formatted = $items->map(function ($item) {
    //         return [
    //             'label' => $item->item_code . ' - ' . $item->item_name,
    //             'value' => $item->item_code,
    //             'name'  => $item->item_name,
    //         ];
    //     });

    //     return response()->json($formatted);
    // }


    

// //stock ledger card
// public function stockLedgerCard(Request $request)
// {
//     $itemCode = $request->query('item_code');

//     if (!$itemCode) {
//         return view('stock_transactions.ledger_card', [
//             'ledger' => [],
//             'itemCode' => '',
//             'itemName' => '',
//             'search' => '',
//         ]);
//     }

//     $transactions = StockTransaction::where('item_code', $itemCode)
//         ->orderBy('transaction_date', 'asc')
//         ->orderBy('id', 'asc')
//         ->get();

//     if ($transactions->isEmpty()) {
//         return view('stock_transactions.ledger_card', [
//             'ledger' => [],
//             'itemCode' => $itemCode,
//             'itemName' => '',
//             'search' => $itemCode,
//         ]);
//     }

//     $itemName = $transactions->first()->item_name;
//     $runningBalance = 0;
//     $ledger = [];

//     foreach ($transactions as $tx) {
//         $qtyIn = $tx->transaction_type === 'IN' ? abs($tx->quantity) : 0;
//         $qtyOut = $tx->transaction_type === 'OUT' ? abs($tx->quantity) : 0;

//         $runningBalance += ($qtyIn - $qtyOut);

//         $ledger[] = [
//             'date' => $tx->transaction_date,
//             'type' => $tx->transaction_type,
//             'qty_in' => $qtyIn,
//             'qty_out' => $qtyOut,
//             'balance' => $runningBalance,
//             'source' => $tx->source,
//             'reference_no' => $tx->reference_no,
//         ];
//     }

//     return view('stock_transactions.ledger_card', [
//         'ledger' => $ledger,
//         'itemCode' => $itemCode,
//         'itemName' => $itemName,
//         'search' => $itemCode
//     ]);
// }

} 

