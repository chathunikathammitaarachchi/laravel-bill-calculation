@extends('layouts.app')

@section('content')

<div class="container" style="max-width: 1200px; margin: auto; padding: 70px; background: linear-gradient(135deg, #e0f7f1 0%, #dbe8f5 100%); border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Create GRN</h2>
        <form action="{{ route('bill.search') }}" method="GET" style="max-width: 350px;">
            @csrf
            <div class="input-group shadow-sm rounded">
                <input type="text" name="grn_no" class="form-control" placeholder="Enter GRN No to Search" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>

    <hr/>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

<form id="grn-form" action="{{ route('bill.store') }}" method="POST">
        @csrf

        <h4>GRN Master</h4>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="grn_no" class="form-label">GRN No</label>
                <input type="text" name="grn_no" id="grn_no" class="form-control" value="{{ old('grn_no', $nextGrnNo) }}" readonly required>
            </div>
            <div class="col-md-4">
                <label for="g_date" class="form-label">GRN Date</label>
                <input type="date" name="g_date" id="g_date" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="supplier_name" class="form-label">Supplier</label>
                <select name="supplier_name" id="supplier_name" class="form-select" required>
                    <option value="" disabled selected>Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_name }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <h4 class="mb-3 text-secondary">GRN Details</h4>

        {{-- Search Fields --}}
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
  <div class="item d-flex align-items-end mb-3 gap-2">
    <div class="form-group" style="flex: 0 0 150px;">
      <label class="form-label">Item Code</label>
      <select name="items[0][item_code]" class="form-select item-code" >
        <option value="" disabled selected>Select</option>
        @foreach($items as $item)
          <option value="{{ $item->item_code }}" data-name="{{ $item->item_name }}" data-rate="{{ $item->rate }}" data-cost-price="{{ $item->cost_price }}">
            {{ $item->item_code }}
          </option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group" style="flex: 0 0 150px;">
      <label class="form-label">Item Name</label>
      <select name="items[0][item_name]" class="form-select item-name" >
        <option value="" disabled selected>Select</option>
        @foreach($items as $item)
          <option value="{{ $item->item_name }}" data-code="{{ $item->item_code }}" data-rate="{{ $item->rate }}">
            {{ $item->item_name }}
          </option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group" style="flex: 0 0 150px;">
      <label class="form-label">Rate</label>
      <input type="number" name="items[0][rate]" class="form-control rate" style="text-align: right;">
    </div>
    
    <div class="form-group" style="flex: 0 0 180px;">
      <label class="form-label">Cost Price</label>
      <input type="number" name="items[0][cost_price]" class="form-control cost_price" style="text-align: right;">
    </div>
    
    <div class="form-group" style="flex: 0 0 180px;">
      <label class="form-label">Quantity</label>
      <input type="number" name="items[0][quantity]" class="form-control quantity" min="1" value="1"  style="text-align: right;">
    </div>
    
    <div class="form-group" style="flex: 0 0 180px;">
      <label class="form-label">Price</label>
      <input type="number" name="items[0][price]" class="form-control price" readonly style="text-align: right;">
    </div>
    
    <div class="col-md-1">
        <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top: 30px;">
            <i class="bi bi-trash"></i>
        </button>
    </div>
  </div>
</div>

<button type="button" class="btn btn-outline-primary mb-4" onclick="addItem()">+ Add Item</button>

        <hr/>

        {{-- Total Calculation Section --}}
        <div class="col-12 col-md-6 offset-md-6 px-3 py-4">
            <div class="mb-3 d-flex align-items-center">
                <label for="total_price" style="width: 40%; font-weight: 600;">Total Price</label>
                <input type="number" name="total_price" id="total_price" readonly class="form-control text-end">
            </div>
            <div class="mb-3 d-flex align-items-center">
                <label for="total_discount" style="width: 40%; font-weight: 600;">Total Discount</label>
                <input type="number" name="total_discount" id="total_discount" value="0" min="0" class="form-control text-end">
            </div>
            <div class="mb-3 d-flex align-items-center">
                <label for="tobe_price" style="width: 40%; font-weight: 600;">To Be Paid</label>
                <input type="number" name="tobe_price" id="tobe_price" readonly class="form-control text-end">
            </div>
            <div class="mb-3 d-flex align-items-center">
                <label for="supplier_pay" style="width: 40%; font-weight: 600;">Supplier Pay</label>
                <input type="number" name="supplier_pay" id="supplier_pay" value="0" min="0" class="form-control text-end">
            </div>
            <div class="mb-3 d-flex align-items-center">
                <label for="balance" style="width: 40%; font-weight: 600;">Balance</label>
                <input type="number" name="balance" id="balance" readonly class="form-control text-end">
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg shadow-sm" style="float: right; width: 20%; font-size: 18px; font-weight: 600; border-radius: 12px;">
            Save GRN
        </button>

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
        </style>
    </form>
</div>

<script>


document.getElementById('grn-form').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item');
    let hasValidItem = false;

    items.forEach((row, index) => {
        const code = row.querySelector('.item-code')?.value?.trim();
        const name = row.querySelector('.item-name')?.value?.trim();
        const qty = row.querySelector('.quantity')?.value?.trim();

        const isValid = code && name && qty && parseFloat(qty) > 0;

        if (isValid) {
            hasValidItem = true;
        } else {
            // Remove empty rows (but keep first if it's the only one)
            if (items.length > 1) {
                row.remove();
            }
        }
    });

    if (!hasValidItem) {
        e.preventDefault();
        alert('Please add at least one valid item before submitting the form.');
    }
});


document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const row = e.target.closest('.item');
        if (row) {
            row.remove();
            calculateTotals();
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('g_date').value = new Date().toISOString().split('T')[0];
});

let itemIndex = 1;

function addItem(code = '', name = '', rate = '', costPrice = '') {
    const template = document.querySelector('.item');
    const clone = template.cloneNode(true);

    clone.querySelectorAll('input, select').forEach(el => {
        const oldName = el.getAttribute('name');
        if (oldName) {
            el.setAttribute('name', oldName.replace(/\[\d+\]/, `[${itemIndex}]`));
        }

        if (el.classList.contains('item-code')) el.value = code;
        else if (el.classList.contains('item-name')) el.value = name;
        else if (el.classList.contains('rate')) el.value = rate;
        else if (el.classList.contains('cost_price')) el.value = costPrice;
        else if (el.classList.contains('quantity')) el.value = code ? 1 : '';
        else if (el.classList.contains('price')) el.value = '';
        else el.value = '';
    });

    document.getElementById('items').appendChild(clone);
    
    if (code) {
        updateRowPrice(clone);
        calculateTotals();
    }
    
    itemIndex++;
    return clone;
}

function updateRowPrice(row) {
    const costPriceInput = row.querySelector('.cost_price');
    const quantityInput = row.querySelector('.quantity');
    const priceInput = row.querySelector('.price');

    if (!costPriceInput || !quantityInput || !priceInput) {
        console.warn('Missing fields in row:', row);
        return;
    }

    const costPrice = parseFloat(costPriceInput.value) || 0;
    const quantity = parseFloat(quantityInput.value) || 0;

    priceInput.value = (costPrice * quantity).toFixed(2);
}

function calculateTotals() {
    let total = 0;
    document.querySelectorAll('.price').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    document.getElementById('total_price').value = total.toFixed(2);

    const discount = parseFloat(document.getElementById('total_discount').value) || 0;
    const toBePaid = total - discount;
    document.getElementById('tobe_price').value = toBePaid.toFixed(2);

    const supplierPay = parseFloat(document.getElementById('supplier_pay').value) || 0;
    document.getElementById('balance').value = (supplierPay - toBePaid).toFixed(2);
}

// Helper functions
function setRowData(row, code, name, rate, costPrice) {
    row.querySelector('.item-code').value = code;
    row.querySelector('.item-name').value = name;
    row.querySelector('.rate').value = rate;
    
    const costPriceInput = row.querySelector('.cost_price');
    if (costPriceInput) {
        costPriceInput.value = costPrice;
    }
    
    row.querySelector('.quantity').value = 1;
    updateRowPrice(row);
    calculateTotals();
}

function focusQuantity(row) {
    setTimeout(() => {
        const qtyInput = row.querySelector('.quantity');
        if (qtyInput) {
            qtyInput.focus();
            qtyInput.select();
        }
    }, 100);
}

function hasEmptyRow() {
    const items = document.querySelectorAll('.item');
    const lastItem = items[items.length - 1];
    return lastItem && !lastItem.querySelector('.item-code').value;
}

document.addEventListener('input', function (e) {
    if (['quantity', 'cost_price'].some(cls => e.target.classList.contains(cls))) {
        updateRowPrice(e.target.closest('.item'));
        calculateTotals();
    }
    
    if (['total_discount', 'supplier_pay'].includes(e.target.id)) {
        calculateTotals();
    }
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('item-code') || e.target.classList.contains('item-name')) {
        const row = e.target.closest('.item');
        const codeSelect = row.querySelector('.item-code');
        const nameSelect = row.querySelector('.item-name');
        const rateInput = row.querySelector('.rate');
        const costPriceInput = row.querySelector('.cost_price');

        if (!codeSelect || !nameSelect || !rateInput || !costPriceInput) return;

        if (e.target.classList.contains('item-code')) {
            const option = codeSelect.selectedOptions[0];
            if (option) {
                const name = option.getAttribute('data-name');
                const rate = option.getAttribute('data-rate');
                const costPrice = option.getAttribute('data-cost-price') || rate;

                nameSelect.value = name;
                rateInput.value = rate;
                costPriceInput.value = costPrice;
            }
        } else {
            const option = nameSelect.selectedOptions[0];
            if (option) {
                const code = option.getAttribute('data-code');
                const rate = option.getAttribute('data-rate');
                const costPrice = option.getAttribute('data-cost-price') || rate;

                codeSelect.value = code;
                rateInput.value = rate;
                costPriceInput.value = costPrice;
            }
        }

        updateRowPrice(row);
        calculateTotals();
        
        if (codeSelect.value && !hasEmptyRow()) {
            addItem();
        }
    }
});

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
        data-code="${item.item_code}" data-name="${item.item_name}" data-rate="${item.rate}" data-cost_price="${item.cost_price}">
        ${item.item_code} - ${item.item_name} - Rs.${item.rate}
    </button>`;
                });
                resultEl.innerHTML = results;
            })
            .catch(error => console.error('Search error:', error));
    });

    resultEl.addEventListener('click', function (e) {
        if (e.target.tagName === 'BUTTON') {
            const code = e.target.getAttribute('data-code');
            const name = e.target.getAttribute('data-name');
            const rate = e.target.getAttribute('data-rate');
            const costPrice = e.target.getAttribute('data-cost_price');

            const existingItem = Array.from(document.querySelectorAll('.item-code')).find(select => select.value === code);

            if (existingItem) {
                const row = existingItem.closest('.item');
                focusQuantity(row);
            } else {
                const emptyRow = Array.from(document.querySelectorAll('.item')).find(item => 
                    !item.querySelector('.item-code').value
                );

                if (emptyRow) {
                    setRowData(emptyRow, code, name, rate, costPrice);
                    
                    addItem();
                    
                    focusQuantity(emptyRow);
                } else {
                    const newRow = addItem(code, name, rate, costPrice);
                    
                    addItem();
                    
                    focusQuantity(newRow);
                }
            }

            inputEl.value = '';
            resultEl.innerHTML = '';
        }
    });
});
</script>

@endsection
