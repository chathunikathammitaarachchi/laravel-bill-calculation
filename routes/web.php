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
use App\Http\Controllers\StockInHandController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\DailyItemSummaryController;
use App\Http\Controllers\SupplierDuePaymentController;
use App\Models\Supplier;
use App\Http\Controllers\CustomerDuePaymentController;

Route::get('/customer-ledger', [CustomerController::class, 'customerLedger'])->name('customer.ledger');
Route::get('/customer-search', [CustomerController::class, 'customerSearch'])->name('customer.search');

// Supplier Ledger Routes
Route::get('/supplier-ledger', [SupplierController::class, 'supplierLedger'])->name('supplier.ledger');
Route::get('/supplier-search', [SupplierController::class, 'supplierSearch'])->name('supplier.search');
Route::get('/supplier-ledger-pdf', [SupplierController::class, 'exportSupplierLedgerPDF'])->name('ledger.supplier.pdf');


Route::get('/cheque/search', [SupplierDuePaymentController::class, 'searchCheque'])->name('cheque.search');
Route::post('/cheque/return/{paymentId}', [SupplierDuePaymentController::class, 'returnCheque'])->name('cheque.return');
Route::get('/cheque/list', [SupplierDuePaymentController::class, 'listCheques'])->name('cheque.list');



Route::get('/stock-in-hand', [StockTransactionController::class, 'stockInHandIndex'])
     ->name('stock_in_hand.index');
Route::get('/itemsummary', [DailyItemSummaryController::class, 'index'])->name('itemsummary');
Route::get('/itemsummary/pdf', [DailyItemSummaryController::class, 'itemsummarydownloadPdf'])->name('itemsummary.pdf');
Route::get('/stock/bin-card', [StockTransactionController::class, 'binCard'])->name('stock.bin_card');
Route::get('/stock-ledger', [StockTransactionController::class, 'stockLedgerCard'])->name('stock.ledger');

Route::get('/stock/pdf', [StockHistoryController::class, 'stokindownloadPdf'])->name('stock.ledger.pdf');
Route::get('/daily-item-summary', [DailyItemSummaryController::class, 'index'])->name('daily.item.summary');
Route::get('/stock-on-hand/pdf', [DailySummaryController::class, 'stockOnHandPdf'])->name('stock.onhand.pdf');



Route::get('/home', function () {
    return view('home');
})->name('home');


Route::get('/api/items/autocomplete', [StockHistoryController::class, 'autocomplete'])->name('items.autocomplete');

Route::get('/bill/dues', [SupplierGRNController::class, 'showDues'])->name('bill.dues');
Route::get('/bill/{grn_no}/edit', [SupplierGRNController::class, 'edit'])->name('bill.edit');





Route::get('customer-dues', [CustomerDuePaymentController::class, 'showDues'])->name('customer_dues.list');
Route::get('customer-dues/{customerName}', [CustomerDuePaymentController::class, 'showByCustomer'])->name('customer_due.show');
Route::post('customer-dues/pay', [CustomerDuePaymentController::class, 'payDueByCustomer'])->name('customer_due.pay');

Route::get('customer-dues/export-pdf', [CustomerDuePaymentController::class, 'customerExportDuesPDF'])->name('customer_dues.export_pdf');




Route::get('/grn/summary', [GRNController::class, 'summaryReport'])->name('grn.summary');
Route::get('/grn/summary/pdf', [GRNController::class, 'summaryReportPDF'])->name('grn.summary.pdf');



Route::get('/daily-summary', [DailySummaryController::class, 'index'])->name('daily.summary');
Route::match(['get', 'post'], '/daily-summary/pdf', [DailySummaryController::class, 'dailydownloadPdf'])->name('daily.summary.pdf');



Route::get('/item-summaries', [ItemSummaryController::class, 'index'])->name('item_summaries.index');
// routes/web.php
Route::post('/item_summaries/download-pdf', [ItemSummaryController::class, 'summarydownloadPDF'])->name('item_summaries.download_pdf');
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
Route::get('/items/check-code', [ItemController::class, 'checkCode']);
Route::get('/items/check-name', [ItemController::class, 'checkName']);
// web.php or api.php
Route::get('/items/ajax', function () {
    return \App\Models\Item::all();
})->name('items.ajax');




Route::get('/grn/report', [GRNController::class, 'report'])->name('grn.report');
Route::get('/grn/report/pdf', [GRNController::class, 'exportPdf'])->name('grn.report.pdf');
Route::get('/stock-transactions/out', [GRNController::class, 'showOutTransactions'])->name('stock.transactions.out');



Route::get('/bill/report', [SupplierGRNController::class, 'gurureport'])->name('bill.report');
Route::get('/bill/report/pdf', [SupplierGRNController::class, 'guruexportPdf'])->name('bill.report.pdf');
Route::get('/stock-transactions/in', [SupplierGRNController::class, 'showInTransactions'])->name('stock.transactions.in');
Route::get('/bill/summary', [SupplierGRNController::class, 'summary'])->name('bill.summary');
Route::get('/bill/details', [SupplierGRNController::class, 'grnDetailsByDate'])->name('bill.details');
Route::get('/bill-details/{date}/pdf', [SupplierGRNController::class, 'downloadGrnPdf'])->name('grn.details.pdf');
Route::get('/bill/summary/pdf', [SupplierGRNController::class, 'downloadSummaryPdf'])->name('bill.summary.pdf');
// routes/web.php
Route::get('/grn-details/pdf', [SupplierGRNController::class, 'downloadGrnPdf'])->name('bill.details.pdf');


// Show list of dues
Route::get('/bill/dues', [SupplierGRNController::class, 'showDues'])->name('bill.dues');

// Show payment form by supplier name
Route::get('/supplier/pay-due/{supplier_name}', [SupplierDuePaymentController::class, 'showBySupplier'])->name('due_payments.form.by.supplier');

// Submit payment form (POST)
Route::post('/supplier/pay-due', [SupplierDuePaymentController::class, 'payDueBySupplier'])->name('due.pay');

// Export dues PDF
Route::get('/bill/dues/export-pdf', [SupplierGRNController::class, 'supplierexportDuesPDF'])->name('bill.dues.export');

// Supplier autocomplete (AJAX)
Route::get('/autocomplete-suppliers', [SupplierDuePaymentController::class, 'autocomplete'])->name('suppliers.autocomplete');

// Success page view after payment (optional)
Route::view('/due-payment/success', 'grn.due_success')->name('due.success');

Route::post('/cheque-return/{id}', [SupplierDuePaymentController::class, 'returnCheque'])->name('cheque.return');
Route::get('/cheque-payments', [SupplierDuePaymentController::class, 'listPayments'])->name('cheque.payments');
// In web.php



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







