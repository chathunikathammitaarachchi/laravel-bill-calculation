
 <!--Create Bill code -->

@extends('layouts.app')

@section('content')

<div class="container" style="max-width: 1200px; margin: auto; padding: 20px;  background: linear-gradient(135deg, #e0e0f8ff 0%, #dbe8f5 100%); border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
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
            <div class="item row align-items-end mb-3">
                <div class="col-md-2">
                    <label class="form-label">Item Code</label>
                    <select name="items[0][item_code]" class="form-select item-code" required style="max-width: 350px;">
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
                    <select name="items[0][item_name]" class="form-select item-name" style="max-width: 350px;" required >
                        <option value="" disabled selected>Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->item_name }}" data-code="{{ $item->item_code }}" data-rate="{{ $item->rate }}">
                                {{ $item->item_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Item Rate</label>
                   <select name="items[0][rate]" class="form-select rate-dropdown" required>
  <option value="" disabled selected>Select Item Rate</option>
  @foreach($rates as $price)
    <option value="{{ $price->rate }}">{{ $price->rate }}</option>
  @endforeach
</select>


                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="items[0][quantity]" class="form-control quantity" min="1" value="1" required style="text-align: right;">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="items[0][price]" class="form-control price" readonly style="text-align: right;">
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-outline-primary mb-4" onclick="addItem()">+ Add Item</button>

        <hr/>
        <div style="max-width: 450px; margin: 20px auto; padding: 20px; font-family: Arial, sans-serif;margin-left:700px;">
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
                        <label for="new_customer_name" class="form-label">Customer Name</label>
                        <input type="text" id="new_customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_customer_phone" class="form-label">Phone</label>
                        <input type="number" name="phone" id="phone" class="form-control" autocomplete="tel" required oninput="limitInput(this, 10)">
                    </div>
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer ID</label>
                        <input type="number" id="customer_id" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
<button type="submit" class="btn btn-success btn-lg shadow-sm" 
        style="width: 20%; font-size: 18px; font-weight: 600; border-radius: 12px; 
               transition: background-color 0.3s ease, transform 0.2s ease; float: right;">
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
</style>                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Set GRN date to today
    document.getElementById('grn_date').value = new Date().toISOString().split('T')[0];
});

let itemIndex = 1;

// This object should be generated server-side and injected properly
const itemRates = @json($items->mapWithKeys(function($item) {
    return [$item->item_code => $item->itemPrices->pluck('rate')->toArray()];
})->toArray());

function addItem(code = '', name = '', rate = '') {
    const itemsDiv = document.getElementById('items');
    const newItem = document.querySelector('.item').cloneNode(true);

    // Remove labels for cleaner UI (optional)
    newItem.querySelectorAll('label').forEach(label => label.remove());

    newItem.querySelectorAll('select, input').forEach(el => {
        // Update name attribute index for form submission
        const nameAttr = el.getAttribute('name');
        if (nameAttr) {
            el.setAttribute('name', nameAttr.replace(/\[\d+\]/, `[${itemIndex}]`));
        }

        if (el.classList.contains('item-code')) {
            el.value = code;
        } else if (el.classList.contains('item-name')) {
            el.value = name;
        } else if (el.classList.contains('rate-dropdown')) {
            el.innerHTML = '<option value="" disabled selected>Select Item Rate</option>';
            if (code && itemRates[code]) {
                itemRates[code].forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r;
                    opt.textContent = r;
                    if (r == rate) opt.selected = true;
                    el.appendChild(opt);
                });
            }
        } else if (el.classList.contains('quantity')) {
            el.value = 1;
        } else if (el.classList.contains('price')) {
            el.value = '';
        } else {
            el.value = '';
        }
    });

    itemsDiv.appendChild(newItem);

    // Calculate initial price and totals
    updateRowPrice(newItem);
    calculateTotals();

    const qtyInput = newItem.querySelector('.quantity');
    if (qtyInput) {
        qtyInput.focus();
        qtyInput.select();
    }

    itemIndex++;
}

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

function updateRowPrice(row) {
    const rate = parseFloat(row.querySelector('.rate-dropdown').value) || 0;
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const priceInput = row.querySelector('.price');
    if (priceInput) {
        priceInput.value = (rate * quantity).toFixed(2);
    }
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

    const customerPay = parseFloat(document.getElementById('customer_pay').value) || 0;
    document.getElementById('balance').value = (customerPay - tobe).toFixed(2);
}

// Event delegation for changes in item code, rate, and quantity dropdowns
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('item-code')) {
        const row = e.target.closest('.item');
        const selectedCode = e.target.value;

        updateRateDropdown(row, selectedCode);

        // Reset rate & price fields
        const rateDropdown = row.querySelector('.rate-dropdown');
        if (rateDropdown) rateDropdown.value = '';

        const priceInput = row.querySelector('.price');
        if (priceInput) priceInput.value = '';

        calculateTotals();
    }

    if (e.target.classList.contains('rate-dropdown') || e.target.classList.contains('quantity')) {
        const row = e.target.closest('.item');
        updateRowPrice(row);
        calculateTotals();
    }
});



const itemNames = @json($items->mapWithKeys(function($item) {
    return [$item->item_code => $item->item_name];
})->toArray());

const itemCodes = @json($items->mapWithKeys(function($item) {
    return [$item->item_name => $item->item_code];
})->toArray());

// Also update totals on input (live update)
document.addEventListener('input', function (e) {
    const row = e.target.closest('.item');

    if (e.target.classList.contains('quantity') || e.target.classList.contains('rate-dropdown')) {
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

            // Also update the rate dropdown when name → code changes
            updateRateDropdown(row, itemCodes[name]);
        }
    }

    if (['total_discount', 'customer_pay'].includes(e.target.id)) {
        calculateTotals();
    }
});


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
        if (e.target.tagName === 'BUTTON') {
            const code = e.target.getAttribute('data-code');
            const name = e.target.getAttribute('data-name');
            const rate = e.target.getAttribute('data-rate');

            const existingItem = Array.from(document.querySelectorAll('.item-code')).find(select => select.value === code);

            if (existingItem) {
                // Already exists – focus quantity field of existing
                const row = existingItem.closest('.item');
                const qtyInput = row.querySelector('.quantity');
                if (qtyInput) {
                    qtyInput.focus();
                    qtyInput.select();
                }
            } else {
                // Check if first row is empty
                const firstRow = document.querySelectorAll('.item').length === 1 && !document.querySelector('.item-code').value;

                if (firstRow) {
                    const row = document.querySelector('.item');

                    row.querySelector('.item-code').value = code;
                    row.querySelector('.item-name').value = name;
                    
                    // Populate rate dropdown and select the rate
                    const rateDropdown = row.querySelector('.rate-dropdown');
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

function limitInput(el, maxLength) {
    if (el.value.length > maxLength) {
        el.value = el.value.slice(0, maxLength);
    }
}

// Add Customer form submission handler
document.getElementById('addCustomerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const name = document.getElementById('new_customer_name').value.trim();
    const phone = document.getElementById('phone').value.trim(); // Correct ID
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

        // Add new customer option to select and select it
        const customerSelect = document.getElementById('customer_name');
        const newOption = document.createElement('option');
        newOption.value = data.customer_name;
        newOption.textContent = data.customer_name;
        customerSelect.appendChild(newOption);
        customerSelect.value = data.customer_name;

    } catch (err) {
        console.error('Add Customer Error:', err);
        alert("Failed to add customer. Check console/logs.");
    }
});

// Inert body when modal is open for accessibility
const modalEl = document.getElementById('addCustomerModal');

modalEl.addEventListener('show.bs.modal', () => {
    document.body.setAttribute('inert', '');
});

modalEl.addEventListener('hidden.bs.modal', () => {
    document.body.removeAttribute('inert');
});
</script>


@endsection
