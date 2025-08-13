
 <!--Create GRN  -->
@extends('layouts.app')

@section('content')

<div>
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

    <form action="{{ route('bill.store') }}" method="POST">
        @csrf

        <h4>GRN Master</h4>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="grn_no" class="form-label">GRN No</label>
                <input type="text" name="grn_no" id="grn_no" class="form-control" 
                       value="{{ old('grn_no', $nextGrnNo) }}" readonly required>
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

<div class="row mb-4">

    
    <div class="col-md-4">
        <label for="search_code" class="form-label">Search Item Code</label>
        <input type="text" id="search_code" class="form-control" placeholder="Search by code">
        <div id="results_code" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
    </div>

    <!-- Search by Name -->
    <div class="col-md-4">
        <label for="search_name" class="form-label">Search Item Name</label>
        <input type="text" id="search_name" class="form-control" placeholder="Search by name">
        <div id="results_name" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
    </div>

    <!-- Search by Rate -->
    <div class="col-md-4">
        <label for="search_rate" class="form-label">Search Item Rate</label>
        <input type="text" id="search_rate" class="form-control" placeholder="Search by rate">
        <div id="results_rate" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
    </div>

</div>


<hr/>


        <div id="items">
            <div class="item row align-items-end mb-3">
                <div class="col-md-2">
                    <label class="form-label">Item Code</label>
                    <select name="items[0][item_code]" class="form-select item-code" required>
                        <option value="" disabled selected>Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->item_code }}" data-name="{{ $item->item_name }}" data-rate="{{ $item->rate }}">
                                {{ $item->item_code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Item Name</label>
                    <select name="items[0][item_name]" class="form-select item-name" required>
                        <option value="" disabled selected>Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->item_name }}" data-code="{{ $item->item_code }}" data-rate="{{ $item->rate }}">
                                {{ $item->item_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rate</label>
                    <input type="number" name="items[0][rate]" class="form-control rate" readonly style="text-align: right;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="items[0][quantity]" class="form-control quantity" min="1" value="1" required style="text-align: right;">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="items[0][price]" class="form-control price"  readonly  style="text-align: right;">
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-outline-primary mb-4" onclick="addItem()">+ Add Item</button>


<hr/>
        <div style="max-width: 450px; margin: 20px auto; margin-left:800px; padding: 20px; font-family: Arial, sans-serif;">
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-start;">
                <label for="total_price" style="width: 40%; font-weight: 600; margin-right: 10px;">Total Price</label>
                <input type="number" name="total_price" id="total_price" readonly
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-start;">
                <label for="total_discount" style="width: 40%; font-weight: 600; margin-right: 10px;">Total Discount</label>
                <input type="number" name="total_discount" id="total_discount" value="0" min="0"
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="tobe_price" style="width: 40%; font-weight: 600; margin-right: 10px;">To Be Paid</label>
                <input type="number" name="tobe_price" id="tobe_price" readonly
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="supplier_pay" style="width: 40%; font-weight: 600; margin-right: 10px;">Supplier Pay</label>
                <input type="number" name="supplier_pay" id="supplier_pay" value="0" min="0"
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: flex-end;">
                <label for="balance" style="width: 40%; font-weight: 600; margin-right: 10px;">Balance</label>
                <input type="number" name="balance" id="balance" readonly
                       style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 5px; text-align: right;">
            </div>
        </div>

        <button type="submit" class="btn btn-success" style="margin-left: 1145px; font-size: 16px;">Save GRN</button>

    </form>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('g_date').value = new Date().toISOString().split('T')[0];
});

let itemIndex = 1;

function addItem(code = '', name = '', rate = '') {
    const itemsDiv = document.getElementById('items');
    const newItem = document.querySelector('.item').cloneNode(true);


    newItem.querySelectorAll('label').forEach(label => label.remove());

    newItem.querySelectorAll('select, input').forEach(el => {
        const nameAttr = el.getAttribute('name');
        if (nameAttr) {
            el.setAttribute('name', nameAttr.replace(/\[\d+\]/, `[${itemIndex}]`));
        }

        if (el.classList.contains('item-code')) {
            el.value = code;
        } else if (el.classList.contains('item-name')) {
            el.value = name;
        } else if (el.classList.contains('rate')) {
            el.value = rate;
        } else if (el.classList.contains('quantity')) {
            el.value = 1;
        } else if (el.classList.contains('price')) {
            el.value = '';
        } else {
            el.value = '';
        }
    });

    itemsDiv.appendChild(newItem);
    updateRowPrice(newItem);
    calculateTotals();

    const qtyInput = newItem.querySelector('.quantity');
    if (qtyInput) {
        qtyInput.focus();
        qtyInput.select();
    }

    itemIndex++;
}

function updateRowPrice(row) {
    const rate = parseFloat(row.querySelector('.rate').value) || 0;
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    row.querySelector('.price').value = (rate * quantity).toFixed(2);
}

function calculateTotals() {
    let total = 0;
    document.querySelectorAll('.price').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    document.getElementById('total_price').value = total.toFixed(2);

    const discount = parseFloat(document.getElementById('total_discount').value) || 0;
    const tobe = total - discount;
    document.getElementById('tobe_price').value = tobe.toFixed(2);

    const supplierPay = parseFloat(document.getElementById('supplier_pay').value) || 0;
    document.getElementById('balance').value = (supplierPay - tobe).toFixed(2);
}

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('item-code') || e.target.classList.contains('item-name')) {
        const row = e.target.closest('.item');
        const itemCodeSelect = row.querySelector('.item-code');
        const itemNameSelect = row.querySelector('.item-name');
        const rateInput = row.querySelector('.rate');

        if (e.target.classList.contains('item-code')) {
            const option = itemCodeSelect.selectedOptions[0];
            const name = option?.getAttribute('data-name') || '';
            const rate = option?.getAttribute('data-rate') || 0;

            itemNameSelect.value = name;
            rateInput.value = rate;
        } else {
            const option = itemNameSelect.selectedOptions[0];
            const code = option?.getAttribute('data-code') || '';
            const rate = option?.getAttribute('data-rate') || 0;

            itemCodeSelect.value = code;
            rateInput.value = rate;
        }

        updateRowPrice(row);
        calculateTotals();
    }
});

document.addEventListener('input', function (e) {
    if (e.target.classList.contains('quantity') || e.target.classList.contains('rate')) {
        updateRowPrice(e.target.closest('.item'));
        calculateTotals();
    }

    if (['total_discount', 'supplier_pay'].includes(e.target.id)) {
        calculateTotals();
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
                            data-code="${item.item_code}" data-name="${item.item_name}" data-rate="${item.rate}">
                            ${item.item_code} - ${item.item_name} - Rs.${item.rate}
                        </button>`;
                });
                resultEl.innerHTML = results;
            });
    });

    resultEl.addEventListener('click', function (e) {
        if (e.target.tagName === 'BUTTON') {
            const code = e.target.getAttribute('data-code');
            const name = e.target.getAttribute('data-name');
            const rate = e.target.getAttribute('data-rate');

            const existingItem = Array.from(document.querySelectorAll('.item-code')).find(select => select.value === code);

            if (existingItem) {
                
                const row = existingItem.closest('.item');
                const qtyInput = row.querySelector('.quantity');
                if (qtyInput) {
                    qtyInput.focus();
                    qtyInput.select();
                }
            } else {
                const firstRow = document.querySelectorAll('.item').length === 1 && !document.querySelector('.item-code').value;

                if (firstRow) {
                    
                    const row = document.querySelector('.item');

                    row.querySelector('.item-code').value = code;
                    row.querySelector('.item-name').value = name;
                    row.querySelector('.rate').value = rate;
                    row.querySelector('.quantity').value = 1;
                    updateRowPrice(row);
                    calculateTotals();

                    const qtyInput = row.querySelector('.quantity');
                    if (qtyInput) {
                        qtyInput.focus();
                        qtyInput.select();
                    }
                } else {
                    
                    addItem(code, name, rate);
                }
            }

            inputEl.value = '';
            resultEl.innerHTML = '';
        }
    });
});
</script>

@endsection
