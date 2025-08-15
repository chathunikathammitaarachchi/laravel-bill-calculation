<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GRNController;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierGRNController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\ItemSummaryController;
use App\Http\Controllers\DailySummaryController;

use App\Http\Controllers\DuePaymentController;

Route::post('/due-payments/pay', [DuePaymentController::class, 'payDue'])->name('due_payments.pay');
Route::view('/due-payment/success', 'grn.due_success')->name('due.success');
Route::get('/due-payments/{id}/pay', [DuePaymentController::class, 'showPaymentForm'])->name('due_payments.form');
Route::get('/grn/dues/export-pdf', [GRNController::class, 'customerexportDuesPDF'])->name('grn.dues.export');


Route::get('/grn/summary', [GRNController::class, 'summaryReport'])->name('grn.summary');
Route::get('/grn/summary/pdf', [GRNController::class, 'summaryReportPDF'])->name('grn.summary.pdf');



Route::get('/grn/dues', [GRNController::class, 'showDues'])->name('grn.dues');
Route::get('/daily-summary', [DailySummaryController::class, 'index'])->name('daily.summary');
Route::get('/daily-summary/pdf', [DailySummaryController::class, 'dailydownloadPDF'])->name('daily.summary.pdf');



Route::get('/item-summaries', [ItemSummaryController::class, 'index'])->name('item_summaries.index');
Route::get('/item-summaries/download-pdf', [\App\Http\Controllers\ItemSummaryController::class, 'summarydownloadPDF'])->name('item_summaries.download_pdf');
Route::get('/stock-movement', [ItemSummaryController::class, 'stockMovement'])->name('stock_movement.index');
Route::get('/item-summary', [ItemSummaryController::class, 'stockMovement'])->name('item-summary');
Route::get('/item-summary/autocomplete', [ItemSummaryController::class, 'autocomplete'])->name('item-summary.autocomplete');





Route::get('/stock/transaction', [StockTransactionController::class, 'transaction'])->name('stock.transactions');
Route::get('/stock-transactions/{type}', [StockTransactionController::class, 'showTransactions']) ->where('type', 'IN|OUT') ->name('stock.transactions.byType');
Route::get('/stock-transactions', [StockTransactionController::class, 'index'])->name('stock.transactions');
Route::get('/pie-chart', [StockTransactionController::class, 'showPieChart'])->name('showPieChart');
Route::get('/stock/transactions/pdf', [StockTransactionController::class, 'stockexportPdf'])->name('stock.transactions.pdf');
Route::get('/stock/download-pdf', [StockTransactionController::class, 'downloadPdf'])->name('stock.download');
Route::get('/stock/transactions', [StockTransactionController::class, 'index'])->name('stock.transactions');


Route::get('/items/stock', [ItemController::class, 'stock'])->name('items.stock');
Route::get('/stock', [ItemController::class, 'stock'])->name('stock.index');
Route::post('/stock/transaction', [ItemController::class, 'addStockTransaction'])->name('stock.transaction');




Route::get('/grn/report', [GRNController::class, 'report'])->name('grn.report');
Route::get('/grn/report/pdf', [GRNController::class, 'exportPdf'])->name('grn.report.pdf');
Route::get('/stock-transactions/out', [GRNController::class, 'showOutTransactions'])->name('stock.transactions.out');



Route::get('/bill/report', [SupplierGRNController::class, 'gurureport'])->name('bill.report');
Route::get('/bill/report/pdf', [SupplierGRNController::class, 'guruexportPdf'])->name('bill.report.pdf');
Route::get('/stock-transactions/in', [SupplierGRNController::class, 'showInTransactions'])->name('stock.transactions.in');


Route::get('/', [GRNController::class, 'create'])->name('grn.create');
Route::post('/store', [GRNController::class, 'store'])->name('grn.store');
Route::get('/grn/search', [GRNController::class, 'search'])->name('grn.search');
Route::post('/grn', [GRNController::class, 'store'])->name('grn.store');
Route::get('/grn/{bill_no}/edit', [GRNController::class, 'edit'])->name('grn.edit');
Route::put('/grn/{bill_no}', [GRNController::class, 'update'])->name('grn.update');
Route::delete('/grn/{bill_no}', [GRNController::class, 'destroy'])->name('grn.destroy');
Route::get('/grn/{bill_no}', [GRNController::class, 'show'])->name('grn.show');


Route::get('/supplier-grn', [SupplierGRNController::class, 'create'])->name('bill.create');
Route::post('/store', [SupplierGRNController::class, 'store'])->name('bill.store');

Route::get('/bill/search', [SupplierGRNController::class, 'search'])->name('bill.search');
Route::post('/bill', [SupplierGRNController::class, 'store'])->name('bill.store');
Route::get('/bill/{grn_no}/edit', [SupplierGRNController::class, 'edit'])->name('bill.edit');
Route::put('/bill/{grn_no}', [SupplierGRNController::class, 'update'])->name('bill.update');
Route::delete('/bill/{grn_no}', [SupplierGRNController::class, 'destroy'])->name('bill.destroy');
Route::get('/bill/{grn_no}', [SupplierGRNController::class, 'show'])->name('bill.show');


Route::resource('items', ItemController::class)->except(['show']);
Route::resource('customer', CustomerController::class);
Route::resource('supplier', SupplierController::class);


Route::get('/items/search/name', [GRNController::class, 'itemNameSearch'])->name('item.search.name');
Route::get('/items/search/code', [GRNController::class, 'itemCodeSearch'])->name('item.search.code');
Route::get('/items/search/price', [GRNController::class, 'itemPriceSearch'])->name('item.search.price');







