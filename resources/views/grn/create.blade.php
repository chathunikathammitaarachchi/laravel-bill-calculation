@extends('layouts.app')

@section('content')
<!-- ✅ SweetAlert2 Include -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container" style="max-width: 1500px; margin: auto; padding: 20px;  background: linear-gradient(135deg, #e0e0f8ff 0%, #dbe8f5 100%); border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Create Bill</h2>

        <form action="{{ route('grn.search') }}" method="GET" style="max-width: 350px;">
            @csrf
            <div class="input-group shadow-sm rounded">
                <input type="text" name="bill_no" class="form-control" placeholder="Enter Bill No to Search" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>

    <hr/>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <!--  Error alert for Cash Due -->
    @if (session('cash_due_error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Payment',
                    text: '{{ session('cash_due_error') }}',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    {{-- Rest of your form and content goes here --}}

    <form action="{{ route('grn.store') }}" method="POST">
        @csrf

        <h4>Bill Master</h4>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="bill_no" class="form-label">Bill No</label>
                <input type="text" name="bill_no" id="bill_no" class="form-control" 
                       value="{{ old('bill_no', $nextBillNo) }}" readonly required>
            </div>
            <div class="col-md-4">
                <label for="grn_date" class="form-label">Bill Date</label>
                <input type="date" name="grn_date" id="grn_date" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="customer_name" class="form-label">Customer</label>
                <div class="input-group">
                    <select name="customer_name" id="customer_name" class="form-select" required>
                        <option value="Cash" selected>Cash</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_name }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                        +
                    </button>
                </div>
            </div>
        </div>

        <h4 class="mb-3 text-secondary">Bill Details</h4>

        <div class="row mb-4">
            <div class="col-md-4">
                <label for="search_code" class="form-label">Search Item Code</label>
                <input type="text" id="search_code" class="form-control" placeholder="Search by code">
                <div id="results_code" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
            </div>

            <div class="col-md-4">
                <label for="search_name" class="form-label">Search Item Name</label>
                <input type="text" id="search_name" class="form-control" placeholder="Search by name">
                <div id="results_name" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
            </div>

            <div class="col-md-4">
                <label for="search_rate" class="form-label">Search Item Rate</label>
                <input type="text" id="search_rate" class="form-control" placeholder="Search by rate">
                <div id="results_rate" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
            </div>
        </div>

        <hr/>

    <div id="items">
    <div class="item row align-items-end mb-2">
        {{-- Item Code --}}
        <div class="col-md-2">
            <label class="form-label">Item Code</label>
            <select name="items[0][item_code]" class="form-select item-code" required>
                <option value="" disabled selected>Select Item</option>
                @foreach($items as $item)
                    <option value="{{ $item->item_code }}" 
                            data-name="{{ $item->item_name }}" 
                            data-rate="{{ $item->rate }}">
                        {{ $item->item_code }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Item Name --}}
        <div class="col-md-3">
            <label class="form-label">Item Name</label>
            <select name="items[0][item_name]" class="form-select item-name" required>
                <option value="" disabled selected>Select Item</option>
                @foreach($items as $item)
                    <option value="{{ $item->item_name }}" 
                            data-code="{{ $item->item_code }}" 
                            data-rate="{{ $item->rate }}">
                        {{ $item->item_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Inside .item row -->
    <div class="form-group" style="flex: 0 0 200px;">
            <label class="form-label">Item Rate</label>
            <div class="input-group">
                <input type="text" name="items[0][rate]" class="form-control rate-input" readonly>
            </div>
        </div>



        {{-- Quantity --}}
    <div class="form-group" style="flex: 0 0 200px;">
            <label class="form-label">Quantity</label>
            <input type="number" name="items[0][quantity]" class="form-control quantity" 
                   min="1" value="1" required style="text-align: right;">
        </div>

    <!-- Price -->
    <div class="form-group" style="flex: 0 0 250px;">
    <label class="form-label"> Our Price</label>
            
    <div class="price-display-container row">
                
    <!-- Final Price Column -->
    <div class="col-6 d-flex flex-column justify-content-center">
        <input type="number"
                name="items[0][price]"
                class="form-control price"
                readonly
                style="text-align: right;" />
    </div>

    <!-- Original Price + Discount Info Column -->
    <div class="col-6 d-flex flex-column align-items-end">
            <label class="form-label"> Discount Price</label>

        <input type="text"
                class="form-control original-price"
                readonly
                style="display: none; font-size: 12px; color: #6c757d; text-decoration: line-through; height: 25px; padding-right: 8px;" />

        <small class="discount-info"
                style="display: none; color: #28a745; font-weight: 500;">
        </small>
    </div>

    </div>
    </div>
        {{-- Remove Button --}}
    <div class="form-group" style="flex: 0 0 10px;">
        <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top: 30px;">
            <i class="bi bi-trash"></i>
        </button>
    </div>
         {{-- Rate --}}
      
<div class="modal fade" id="rateSelectModal" tabindex="-1" aria-labelledby="rateSelectModalLabel">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="rateSelectModalLabel">Select Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Scrollable body -->
            <div class="modal-body p-3 custom-scroll">
    <div id="rate-options" class="list-group">
        <!-- Rates will be injected here -->
    </div>
</div>

        </div>
    </div>
</div>
    </div>
</div>

        <button type="button" class="btn btn-outline-primary mb-4" onclick="addItem()">+ Add Item</button>

        <hr/>
<div class="col-12 col-md-6 offset-md-6 px-3 py-4" style="max-width: 100%; font-family: Arial, sans-serif;">
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-start;">
                <label for="total_price" style="width: 40%; font-weight: 600; margin-right: 10px;">Total Price</label>
                <input type="number" name="total_price" id="total_price" readonly
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
         
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-start;">
            <label for="total_discount" style="width: 40%; font-weight: 600; margin-right: 10px;">Total Discount</label>
            <input type="number" name="total_discount" id="total_discount" readonly
                style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>

            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="tobe_price" style="width: 40%; font-weight: 600; margin-right: 10px;">To Be Paid</label>
                <input type="number" name="tobe_price" id="tobe_price" readonly
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="customer_pay" style="width: 40%; font-weight: 600; margin-right: 10px;">Customer Pay</label>
                <input type="number" name="customer_pay" id="customer_pay" value="0" min="0"
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="balance" style="width: 40%; font-weight: 600; margin-right: 10px;">Balance</label>
                <input type="number" name="balance" id="balance" readonly
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="received_by" style="width: 40%; font-weight: 600; margin-right: 10px;">Received By</label>
                <input type="text" name="received_by" id="received_by" required
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="issued_by" style="width: 40%; font-weight: 600; margin-right: 10px;">Issued By</label>
                <input type="text" name="issued_by" id="issued_by" required
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-end gap-2" style="margin-top: 30px;">
            <button type="submit" class="btn btn-success px-4">Save Bill</button>
            <button type="button" class="btn btn-secondary px-4" onclick="refreshPage()">Refresh</button>
        </div>
    </form>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel">
    <div class="modal-dialog">
        <form id="addCustomerForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer ID</label>
                        <input type="number" id="customer_id" name="customer_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_customer_name" class="form-label">Customer Name</label>
                        <input type="text" id="new_customer_name" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" name="phone" id="phone" class="form-control" autocomplete="tel" required maxlength="10">
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Customer</button>
                </div>
            </div>
        </form>
    </div>
</div>


<style>
    
  button.btn-success:hover {
    background-color: #28a745cc !important;
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(40,167,69,0.35);
  }

  button.btn-success:active {
    transform: scale(0.97);
    box-shadow: none;
  }



.custom-scroll {
    max-height: 200px;
    overflow-y: auto;
    scrollbar-width: none;          
    -ms-overflow-style: none;       
}

.custom-scroll::-webkit-scrollbar {
    display: none;                  
}

</style>      
<script>          
function refreshPage() {
    window.location.reload();
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const row = e.target.closest('.item.row');
        if (row) {
            row.remove();
            calculateTotals(); // Recalculate after removing item
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // Set GRN date to today
    document.getElementById('grn_date').value = new Date().toISOString().split('T')[0];
});

let itemIndex = 1;

// Pass the discount data from PHP to JavaScript
const itemDiscounts = {!! json_encode($items->mapWithKeys(function($item) {
    return [$item->item_code => [
        'discount_1_qty' => $item->discount_1_qty ?? null,
        'discount_1' => $item->discount_1 ?? 0,
        'discount_2_qty' => $item->discount_2_qty ?? null,
        'discount_2' => $item->discount_2 ?? 0,
        'discount_3_qty' => $item->discount_3_qty ?? null,
        'discount_3' => $item->discount_3 ?? 0,
    ]];
})->toArray()) !!};

// This object should be generated server-side and injected properly
const itemRates = @json($items->mapWithKeys(function($item) {
    return [$item->item_code => $item->itemPrices->pluck('rate')->toArray()];
})->toArray());

const itemNames = @json($items->mapWithKeys(function($item) {
    return [$item->item_code => $item->item_name];
})->toArray());

const itemCodes = @json($items->mapWithKeys(function($item) {
    return [$item->item_name => $item->item_code];
})->toArray());

function addItem(code = '', name = '', rate = '') {
    const itemsDiv = document.getElementById('items');
    const newItem = document.querySelector('.item').cloneNode(true);

    // Remove labels
    newItem.querySelectorAll('label').forEach(label => label.remove());

    newItem.querySelectorAll('select, input').forEach(el => {
        // Update name index
        const na = el.getAttribute('name');
        if (na) el.setAttribute('name', na.replace(/\[\d+\]/, `[${itemIndex}]`));

        if (el.classList.contains('item-code')) el.value = code;
        else if (el.classList.contains('item-name')) el.value = name;
        else if (el.classList.contains('rate-input')) el.value = rate;
        else if (el.classList.contains('rate-dropdown')) {
            el.innerHTML = '<option disabled selected>Select Rate</option>';
            if (code && itemRates[code]) {
                itemRates[code].forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r;
                    opt.textContent = r;
                    if (r == rate) opt.selected = true;
                    el.appendChild(opt);
                });
            }
        } else if (el.classList.contains('quantity')) el.value = 1;
        else if (el.classList.contains('price')) el.value = '';
        else if (el.classList.contains('original-price')) {
            el.value = '';
            el.style.display = 'none';
        }
        else el.value = '';
    });

    // Reset discount info display for new row
    const discountInfo = newItem.querySelector('.discount-info');
    if (discountInfo) {
        discountInfo.textContent = '';
        discountInfo.style.display = 'none';
    }

    function focusAndSelect(el) {
        if (!el) return;
        el.focus();
        if (typeof el.select === 'function') {
            el.select();
        }
    }

    itemsDiv.appendChild(newItem);

    // Update price with discount calculation for the new item
    if (code && rate) {
        updateRowPrice(newItem);
        calculateTotals();
    }

    // Focus and select the item-code input of the new row safely
    setTimeout(() => {
        focusAndSelect(newItem.querySelector('.item-code') || newItem.querySelector('.item-name'));
    }, 100);

    itemIndex++;
    console.log("New item added. Current itemIndex:", itemIndex);
    
    return newItem;
}

// Search input configurations
const searchConfigs = [
    { inputId: 'search_code', resultId: 'results_code', endpoint: '/items/search/code' },
    { inputId: 'search_name', resultId: 'results_name', endpoint: '/items/search/name' },
    { inputId: 'search_rate', resultId: 'results_rate', endpoint: '/items/search/price' }
];

searchConfigs.forEach(config => {
    const inputEl = document.getElementById(config.inputId);
    const resultEl = document.getElementById(config.resultId);

    inputEl.addEventListener('input', function () {
        const query = this.value.trim();
        if (query.length < 1) {
            resultEl.innerHTML = '';
            return;
        }

        fetch(`${config.endpoint}?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                let results = '';
                data.forEach(item => {
                    results += `
                        <button type="button" class="list-group-item list-group-item-action"
                            data-code="${item.item_code}" data-name="${item.item_name}" data-rate="${item.rate}">
                            ${item.item_code} - ${item.item_name} - Rs.${item.rate}
                        </button>`;
                });
                resultEl.innerHTML = results;
            });
    });

    resultEl.addEventListener('click', function (e) {
        if (e.target.tagName !== 'BUTTON') return;

        const code = e.target.getAttribute('data-code');
        const name = e.target.getAttribute('data-name');
        const rate = e.target.getAttribute('data-rate');

        // Check if the item is already in the list
        const existingItem = Array.from(document.querySelectorAll('.item-code')).find(select => select.value === code);

        if (existingItem) {
            // Item already exists → focus quantity
            const row = existingItem.closest('.item');
            const qtyInput = row.querySelector('.quantity');
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
            }
        } else {
            // Look for an empty row
            const emptyRow = Array.from(document.querySelectorAll('.item')).find(row => {
                const codeVal = row.querySelector('.item-code')?.value.trim();
                const nameVal = row.querySelector('.item-name')?.value.trim();
                const rateVal = row.querySelector('.rate-input, .rate-dropdown')?.value.trim();
                const qtyVal = row.querySelector('.quantity')?.value.trim();
                return !codeVal && !nameVal && (!rateVal || rateVal === '') && (!qtyVal || qtyVal === '1' || qtyVal === '');
            });

            let targetRow;

            if (emptyRow) {
                // Use the empty row
                targetRow = emptyRow;
            } else {
                // Add new row and use it
                targetRow = addItem(); 
            }

            const codeInput = targetRow.querySelector('.item-code');
            const nameInput = targetRow.querySelector('.item-name');
            const rateInput = targetRow.querySelector('.rate-input');
            const rateDropdown = targetRow.querySelector('.rate-dropdown');
            const quantityInput = targetRow.querySelector('.quantity');

            if (codeInput) codeInput.value = code;
            if (nameInput) nameInput.value = name;

            if (rateDropdown) {
                rateDropdown.innerHTML = '<option value="" disabled>Select Item Rate</option>';
                if (itemRates[code]) {
                    itemRates[code].forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r;
                        opt.textContent = r;
                        if (r == rate) opt.selected = true;
                        rateDropdown.appendChild(opt);
                    });
                }
            }

            if (quantityInput) quantityInput.value = 1;

            // Check if item has multiple rates - show modal for selection
            const availableRates = itemRates[code] || [];
            if (availableRates.length > 1) {
                // Multiple rates available - show selection modal
                showRateModalForSearch(code, targetRow, () => {
                    // Callback after rate selection
                    updateRowPrice(targetRow);
                    calculateTotals();
                    
                    if (quantityInput) {
                        quantityInput.focus();
                        quantityInput.select();
                    }
                });
            } else {
                // Single rate or no rates - set directly
                if (rateInput) rateInput.value = rate;
                
                updateRowPrice(targetRow);
                calculateTotals();

                if (quantityInput) {
                    quantityInput.focus();
                    quantityInput.select();
                }
            }
        }

        inputEl.value = '';
        resultEl.innerHTML = '';
    });
});

// New function specifically for search rate selection
function showRateModalForSearch(itemCode, targetRow, callback) {
    const rates = itemRates[itemCode] || [];

    if (rates.length === 0) return;
    
    if (rates.length === 1) {
        // Single rate - set directly and call callback
        const rateInput = targetRow.querySelector('.rate-input');
        if (rateInput) rateInput.value = rates[0];
        
        const qtyInput = targetRow.querySelector('.quantity');
        if (qtyInput && (!qtyInput.value || qtyInput.value == 0)) {
            qtyInput.value = 1;
        }
        
        if (callback) callback();
        return;
    }

    const rateOptionsContainer = document.getElementById('rate-options');
    rateOptionsContainer.innerHTML = '';
    currentFocusedIndex = 0;

    rates.forEach(rate => {
        const btn = document.createElement('button');
        btn.classList.add('list-group-item', 'list-group-item-action');
        btn.textContent = `Rs. ${rate}`;
        btn.dataset.rate = rate;
        btn.setAttribute('tabindex', '0');
        rateOptionsContainer.appendChild(btn);
    });

    // Store reference for search-specific handling
    currentRateInputForSearch = targetRow.querySelector('.rate-input');
    currentSearchCallback = callback;
    currentTargetRow = targetRow;

    const modal = new bootstrap.Modal(document.getElementById('rateSelectModal'));
    modal.show();

    setTimeout(() => {
        const firstButton = document.querySelector('#rate-options button');
        if (firstButton) {
            firstButton.focus();
        }
    }, 200);
}

function addItemIfLastFilled(row) {
    const itemCode = row.querySelector('.item-code')?.value.trim();
    const itemName = row.querySelector('.item-name')?.value.trim();
    const rateEl = row.querySelector('.rate-input, .rate-dropdown');
    const rate = rateEl ? rateEl.value.trim() : '';
    const qty = row.querySelector('.quantity')?.value.trim();

    const isFilled = itemCode && itemName && rate && qty;

    const allRows = document.querySelectorAll('.item');
    const lastRow = allRows[allRows.length - 1];

    if (isFilled && row === lastRow) {
        addItem();
    }
}

function calculateTotals() {
    let total = 0;
    let totalItemDiscounts = 0;

    document.querySelectorAll('.item').forEach(row => {
        const priceInput = row.querySelector('.price');
        const itemDiscount = parseFloat(row.getAttribute('data-item-discount')) || 0;
        
        total += parseFloat(priceInput?.value) || 0;
        totalItemDiscounts += itemDiscount;
    });

    document.getElementById('total_price').value = total.toFixed(2);

    const additionalDiscount = 0; 
    const totalDiscount = totalItemDiscounts + additionalDiscount;

    document.getElementById('total_discount').value = totalDiscount.toFixed(2);

    const originalPrice = total + totalItemDiscounts;
    const tobe = originalPrice - totalDiscount;

    document.getElementById('tobe_price').value = tobe.toFixed(2);

    const customerPay = parseFloat(document.getElementById('customer_pay').value) || 0;
    document.getElementById('balance').value = (customerPay - tobe).toFixed(2);
}

document.addEventListener('input', function (e) {
    const row = e.target.closest('.item');

    if (e.target.classList.contains('quantity') || 
        e.target.classList.contains('rate-input') || 
        e.target.classList.contains('rate-dropdown')) {
        updateRowPrice(row);
        calculateTotals();
    }

    if (e.target.classList.contains('item-code')) {
        const code = e.target.value.trim();
        const nameInput = row.querySelector('.item-name');
        if (nameInput && itemNames[code]) {
            nameInput.value = itemNames[code];
        }
    }

    if (e.target.classList.contains('item-name')) {
        const name = e.target.value.trim();
        const codeInput = row.querySelector('.item-code');
        if (codeInput && itemCodes[name]) {
            codeInput.value = itemCodes[name];
            updateRateDropdown(row, itemCodes[name]);
        }
    }

    if (['total_discount', 'customer_pay'].includes(e.target.id)) {
        calculateTotals();
    }
});

document.addEventListener('input', function (e) {
    if (e.target.classList.contains('item-code') || e.target.classList.contains('item-name')) {
        const row = e.target.closest('.item');
        const code = row.querySelector('.item-code')?.value;
        const name = row.querySelector('.item-name')?.value;

        const itemCode = code || itemCodes[name];
        if (!itemCode) return;

        const rateInput = row.querySelector('.rate-input');
        showRateModal(itemCode, rateInput);

        setTimeout(() => {
            const allFilled = row.querySelector('.item-code')?.value &&
                              row.querySelector('.item-name')?.value &&
                              row.querySelector('.rate-input')?.value &&
                              row.querySelector('.quantity')?.value;

            if (allFilled) {
                addItem(); 
            }
        }, 200); 
    }
});

function showRateModal(itemCode, targetRateInput) {
    const rates = itemRates[itemCode] || [];

    if (rates.length === 0) return;
    if (rates.length === 1) {
        targetRateInput.value = rates[0];
        const row = targetRateInput.closest('.item');

        const qtyInput = row.querySelector('.quantity');
        if (qtyInput && (!qtyInput.value || qtyInput.value == 0)) {
            qtyInput.value = 1;
        }

        updateRowPrice(row);
        calculateTotals();

        const event = new Event('input', { bubbles: true });
        targetRateInput.dispatchEvent(event);

        return;
    }

    const rateOptionsContainer = document.getElementById('rate-options');
    rateOptionsContainer.innerHTML = '';
    currentFocusedIndex = 0;

    rates.forEach(rate => {
        const btn = document.createElement('button');
        btn.classList.add('list-group-item', 'list-group-item-action');
        btn.textContent = `Rs. ${rate}`;
        btn.dataset.rate = rate;
        btn.setAttribute('tabindex', '0');
        rateOptionsContainer.appendChild(btn);
    });

    currentRateInput = targetRateInput;

    const modal = new bootstrap.Modal(document.getElementById('rateSelectModal'));
    modal.show();

    setTimeout(() => {
        const firstButton = document.querySelector('#rate-options button');
        if (firstButton) {
            firstButton.focus();
        }
    }, 200);
}

function updateRowPrice(row) {
    if (!row) return 0;

    const rateEl = row.querySelector('.rate-input, .rate-dropdown');
    const rate = parseFloat(rateEl?.value) || 0;
    const quantity = parseFloat(row.querySelector('.quantity')?.value) || 0;
    const itemCode = row.querySelector('.item-code')?.value;
    const priceInput = row.querySelector('.price');
    const originalPriceInput = row.querySelector('.original-price');
    const discountInfo = row.querySelector('.discount-info');
    
    if (!priceInput || !itemCode || rate === 0 || quantity === 0) {
        // Clear all price displays when invalid
        if (priceInput) priceInput.value = '';
        if (originalPriceInput) {
            originalPriceInput.style.display = 'none';
            originalPriceInput.value = '';
        }
        if (discountInfo) {
            discountInfo.style.display = 'none';
            discountInfo.textContent = '';
        }
        return 0;
    }

    let basePrice = rate * quantity;
    let discountPerUnit = 0;

    if (itemDiscounts[itemCode]) {
        const discounts = itemDiscounts[itemCode]; 

        if (discounts.discount_3_qty !== null && quantity >= discounts.discount_3_qty) {
            discountPerUnit = discounts.discount_3 || 0;
        } else if (discounts.discount_2_qty !== null && quantity >= discounts.discount_2_qty) {
            discountPerUnit = discounts.discount_2 || 0;
        } else if (discounts.discount_1_qty !== null && quantity >= discounts.discount_1_qty) {
            discountPerUnit = discounts.discount_1 || 0;
        }
    }

    let discount = discountPerUnit * quantity;
    const finalPrice = Math.max(basePrice - discount, 0); 

    // Set the final discounted price
    priceInput.value = finalPrice.toFixed(2);
    
    // Handle original price and discount info display
    if (discount > 0) {
        // Show original price (crossed out)
        if (originalPriceInput) {
            originalPriceInput.value = `Rs. ${basePrice.toFixed(2)}`;
            originalPriceInput.style.display = 'block';
        }
        
        // Show discount information
        if (discountInfo) {
            discountInfo.textContent = `Discount: Rs. ${discount.toFixed(2)} (Rs. ${discountPerUnit.toFixed(2)} per unit)`;
            discountInfo.style.display = 'block';
        }
        
        console.log(`Item: ${itemCode}, Qty: ${quantity}, Rate: ${rate}, Base: Rs.${basePrice}, Unit Discount: Rs.${discountPerUnit}, Total Discount: Rs.${discount}, Final: Rs.${finalPrice}`);
    } else {
        // Hide original price and discount info when no discount
        if (originalPriceInput) {
            originalPriceInput.style.display = 'none';
            originalPriceInput.value = '';
        }
        if (discountInfo) {
            discountInfo.style.display = 'none';
            discountInfo.textContent = '';
        }
    }
    
    row.setAttribute('data-item-discount', discount);
    return discount;
}

// Global variables for search rate selection
let currentRateInputForSearch = null;
let currentSearchCallback = null;
let currentTargetRow = null;

// Modified rate selection handler to support both regular input and search
document.getElementById('rate-options').addEventListener('click', function (e) {
    if (e.target.tagName === 'BUTTON') {
        const selectedRate = e.target.dataset.rate;

        // Handle search-based rate selection
        if (currentRateInputForSearch && currentTargetRow) {
            const row = currentTargetRow;
            const qtyInput = row.querySelector('.quantity');
            
            if (qtyInput && (!qtyInput.value || parseFloat(qtyInput.value) === 0)) {
                qtyInput.value = 1;
            }

            currentRateInputForSearch.value = selectedRate;

            const event = new Event('input', { bubbles: true });
            currentRateInputForSearch.dispatchEvent(event);

            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('rateSelectModal'));
            modalInstance.hide();

            // Execute search callback
            if (currentSearchCallback) {
                currentSearchCallback();
            }

            // Clear search-specific variables
            currentRateInputForSearch = null;
            currentSearchCallback = null;
            currentTargetRow = null;
            
            return;
        }

        // Handle regular input-based rate selection (existing functionality)
        if (currentRateInput) {
            const row = currentRateInput.closest('.item');

            const qtyInput = row.querySelector('.quantity');
            if (qtyInput && (!qtyInput.value || parseFloat(qtyInput.value) === 0)) {
                qtyInput.value = 1;
            }

            currentRateInput.value = selectedRate;

            const event = new Event('input', { bubbles: true });
            currentRateInput.dispatchEvent(event);

            updateRowPrice(row);
            calculateTotals();

            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('rateSelectModal'));
            modalInstance.hide();

            setTimeout(() => {
                addItemIfLastFilled(row);

                const allRows = document.querySelectorAll('.item');
                const lastRow = allRows[allRows.length - 1];
                const codeInput = lastRow.querySelector('.item-code') || lastRow.querySelector('.item-name');

                if (codeInput && typeof codeInput.select === 'function') {
                    codeInput.focus();
                    codeInput.select();
                } else {
                    codeInput?.focus();
                }
            }, 300);
        }
    }
});

function updateRateDropdown(row, itemCode) {
    const rateDropdown = row.querySelector('.rate-dropdown');
    if (!rateDropdown) return;

    rateDropdown.innerHTML = '<option value="" disabled selected>Select Item Rate</option>';

    if (itemRates[itemCode]) {
        itemRates[itemCode].forEach(rate => {
            const option = document.createElement('option');
            option.value = rate;
            option.textContent = rate;
            rateDropdown.appendChild(option);
        });
    }
}

let currentFocusedIndex = 0;
let currentRateInput = null;

document.getElementById('rateSelectModal').addEventListener('keydown', function (e) {
    const rateButtons = Array.from(document.querySelectorAll('#rate-options button'));

    if (rateButtons.length === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        currentFocusedIndex = (currentFocusedIndex + 1) % rateButtons.length;
        rateButtons[currentFocusedIndex].focus();
    }

    if (e.key === 'ArrowUp') {
        e.preventDefault();
        currentFocusedIndex = (currentFocusedIndex - 1 + rateButtons.length) % rateButtons.length;
        rateButtons[currentFocusedIndex].focus();
    }

    if (e.key === 'Enter') {
        e.preventDefault();
        rateButtons[currentFocusedIndex].click(); 
    }

    if (e.key === 'Escape') {
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('rateSelectModal'));
        modalInstance.hide();
        
        // Clear search-specific variables on escape
        currentRateInputForSearch = null;
        currentSearchCallback = null;
        currentTargetRow = null;
    }
});








function limitInput(el, maxLength) {
    if (el.value.length > maxLength) {
        el.value = el.value.slice(0, maxLength);
    }
}

// Add Customer form submission handler
document.getElementById('addCustomerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const name = document.getElementById('new_customer_name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const customerId = parseInt(document.getElementById('customer_id').value, 10);

    try {
        const resp = await fetch('{{ route('customer.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                customer_id: customerId,
                customer_name: name,
                phone: phone,
            }),
        });

        const contentType = resp.headers.get("content-type");

        if (!resp.ok) {
            const errorText = await resp.text();
            console.error("Raw error response:", errorText);
            throw new Error("Failed to submit");
        }

        if (!contentType || !contentType.includes("application/json")) {
            const raw = await resp.text();
            console.error("Expected JSON, got HTML:", raw);
            throw new Error("Server returned HTML instead of JSON.");
        }

        const data = await resp.json();
        console.log("Success:", data);

        const modalEl = document.getElementById('addCustomerModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            document.activeElement.blur();
            modalInstance.hide();
        }

        this.reset();

        const customerSelect = document.getElementById('customer_name');
        const newOption = document.createElement('option');
        newOption.value = data.customer_name;
        newOption.textContent = data.customer_name;
        customerSelect.appendChild(newOption);
        customerSelect.value = data.customer_name;

    } catch (err) {
        console.error('Add Customer Error:', err);
    }
});

// Modal event handlers for accessibility
const modalEl = document.getElementById('addCustomerModal');
if (modalEl) {
    modalEl.addEventListener('show.bs.modal', () => {
        setTimeout(() => {
            const firstInput = modalEl.querySelector('input');
            if (firstInput) firstInput.focus();
        }, 100);
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        const form = document.getElementById('addCustomerForm');
        if (form) form.reset();
    });
}

</script>
@endsection