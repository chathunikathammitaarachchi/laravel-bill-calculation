<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;

class StockHistoryController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $itemCode = $request->input('item_code');
        $itemName = $request->input('item_name');

        $grouped = [];
        $balance = [];
        $inTotal = [];
        $outTotal = [];
        $openingInBalance = [];
        $openingOutBalance = [];

        // Get opening balance before start date, filter by item_code or item_name if given
        if ($start) {
            $openingTransactions = StockTransaction::where('transaction_date', '<', $start);

            if ($itemCode) {
                $openingTransactions->where('item_code', 'like', "%$itemCode%");
            }
            if ($itemName) {
                $openingTransactions->where('item_name', 'like', "%$itemName%");
            }

            $openingTransactions = $openingTransactions->get();

            foreach ($openingTransactions as $tx) {
                $code = $tx->item_code;

                $openingInBalance[$code] = $openingInBalance[$code] ?? 0;
                $openingOutBalance[$code] = $openingOutBalance[$code] ?? 0;

                if ($tx->transaction_type === 'IN') {
                    $openingInBalance[$code] += $tx->quantity;
                } else {
                    $openingOutBalance[$code] += $tx->quantity;
                }
            }
        }

        $transactions = StockTransaction::query();

        if ($start && $end) {
            $transactions->whereBetween('transaction_date', [$start, $end]);
        }

        if ($itemCode) {
            $transactions->where('item_code', 'like', "%$itemCode%");
        }

        if ($itemName) {
            $transactions->where('item_name', 'like', "%$itemName%");
        }

        $transactions = $transactions->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        foreach ($transactions as $tx) {
            $code = $tx->item_code;

            $balance[$code] = $balance[$code] ?? (
                ($openingInBalance[$code] ?? 0) - ($openingOutBalance[$code] ?? 0)
            );
            $inTotal[$code] = $inTotal[$code] ?? 0;
            $outTotal[$code] = $outTotal[$code] ?? 0;

            if ($tx->transaction_type === 'IN') {
                $qty = $tx->quantity;
                $inTotal[$code] += $qty;
            } else {
                $qty = -$tx->quantity;
                $outTotal[$code] += abs($qty);
            }

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
            'inTotal' => $inTotal,
            'outTotal' => $outTotal,
            'openingInBalance' => $openingInBalance,
            'openingOutBalance' => $openingOutBalance,
            'itemCode' => $itemCode,
            'itemName' => $itemName,
        ]);
    }

    public function stokindownloadPdf(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $itemCode = $request->input('item_code');
        $itemName = $request->input('item_name');

        $grouped = [];
        $balance = [];
        $inTotal = [];
        $outTotal = [];
        $openingInBalance = [];
        $openingOutBalance = [];

        if ($start) {
            $openingTransactions = StockTransaction::where('transaction_date', '<', $start);

            if ($itemCode) {
                $openingTransactions->where('item_code', 'like', "%$itemCode%");
            }
            if ($itemName) {
                $openingTransactions->where('item_name', 'like', "%$itemName%");
            }

            $openingTransactions = $openingTransactions->get();

            foreach ($openingTransactions as $tx) {
                $code = $tx->item_code;

                $openingInBalance[$code] = $openingInBalance[$code] ?? 0;
                $openingOutBalance[$code] = $openingOutBalance[$code] ?? 0;

                if ($tx->transaction_type === 'IN') {
                    $openingInBalance[$code] += $tx->quantity;
                } else {
                    $openingOutBalance[$code] += $tx->quantity;
                }
            }
        }

        $transactions = StockTransaction::query();

        if ($start && $end) {
            $transactions->whereBetween('transaction_date', [$start, $end]);
        }

        if ($itemCode) {
            $transactions->where('item_code', 'like', "%$itemCode%");
        }

        if ($itemName) {
            $transactions->where('item_name', 'like', "%$itemName%");
        }

        $transactions = $transactions->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        foreach ($transactions as $tx) {
            $code = $tx->item_code;

            $balance[$code] = $balance[$code] ?? (
                ($openingInBalance[$code] ?? 0) - ($openingOutBalance[$code] ?? 0)
            );
            $inTotal[$code] = $inTotal[$code] ?? 0;
            $outTotal[$code] = $outTotal[$code] ?? 0;

            if ($tx->transaction_type === 'IN') {
                $qty = $tx->quantity;
                $inTotal[$code] += $qty;
            } else {
                $qty = -$tx->quantity;
                $outTotal[$code] += abs($qty);
            }

            $balance[$code] += $qty;

            $grouped[$code][] = [
                'date'    => $tx->transaction_date,
                'name'    => $tx->item_name,
                'type'    => $tx->transaction_type,
                'qty'     => $qty,
                'balance' => $balance[$code],
            ];
        }

        $pdf = Pdf::loadView('stock_history.pdf', [
            'groupedHistory' => $grouped,
            'start' => $start,
            'end' => $end,
            'inTotal' => $inTotal,
            'outTotal' => $outTotal,
            'openingInBalance' => $openingInBalance,
            'openingOutBalance' => $openingOutBalance,
            'itemCode' => $itemCode,
            'itemName' => $itemName,
        ]);

        return $pdf->download('stock_ledger.pdf');
    }
}
