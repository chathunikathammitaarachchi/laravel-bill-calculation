@extends('layouts.app')

@section('content')
<style>
    h2, h4 {
        font-weight: 600;
        color: #333;
    }

    .container {
        background: #ffffff;
        padding: 70px;
        border-radius: 8px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
    }

    table th {
        background-color: #f8f9fa;
        text-align: center;
        vertical-align: middle;
    }

    table td input {
        border: 1px solid #ced4da;
        padding: 6px 8px;
    }

    .form-label {
        font-weight: 500;
        color: #555;
    }

    .form-control:read-only {
        background-color: #e9ecef;
    }

    .btn-danger.btn-sm {
        padding: 4px 8px;
        font-size: 0.875rem;
    }

    .btn-secondary {
        margin-top: 10px;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-primary {
        padding: 8px 20px;
        font-weight: 500;
        font-size: 1rem;
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }

    #itemsTable input {
        text-align: center;
    }

    .d-flex label {
        font-weight: 500;
    }

    .form-control {
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    .float-end {
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        table th, table td {
            font-size: 0.875rem;
        }
    }
</style>


<div class="container mt-4">

    <h2>Edit GRN - GRN No: {{ $bill->GRN_no }}</h2>
    <form action="{{ route('bill.update', $bill->grn_no) }}" method="POST" id="grnForm">
        @csrf
        @method('PUT')

        <div class="mb-3" style="max-width: 300px;">
            <label for="grn_no" class="form-label">GRN No</label>
            <input type="text" name="grn_no" id="grn_no" class="form-control" value="{{ $bill->grn_no }}" readonly>
        </div>

        <div class="mb-3" style="max-width: 300px;">
            <label for="g_date" class="form-label">Date</label>
            <input type="date" name="g_date" id="g_date" class="form-control" value="{{ old('g_date', \Carbon\Carbon::parse($bill->g_date)->format('Y-m-d')) }}" readonly>
        </div>

        <div class="mb-3" style="max-width: 300px;">
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input type="text" name="supplier_name" id="supplier_name" class="form-control" value="{{ $bill->supplier_name }}" readonly>
        </div>

        <hr>

        <h4>Items</h4>
        <table class="table" id="itemsTable">
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Rate</th>                    <th>Cost Price</th>

                    <th>Quantity</th>
                    <th>Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->details as $index => $detail)
                    <tr class="item-row">
                        <td>
    <select name="details[{{ $index }}][item_code]" class="form-control item-select" required>
        <option value="">-- Select Item --</option>
        {{-- You can optionally populate server-side for the initial rows --}}
        @foreach($items as $item)
            <option value="{{ $item->item_code }}" {{ $item->item_code == $detail->item_code ? 'selected' : '' }}>
                {{ $item->item_code }} - {{ $item->item_name }}
            </option>
        @endforeach
    </select>
</td>

                        <td><input type="text" name="details[{{ $index }}][item_name]" value="{{ $detail->item_name }}" class="form-control" readonly></td>
                        <td><input type="number" name="details[{{ $index }}][rate]" value="{{ $detail->rate }}" class="form-control rate" readonly style="text-align: right;"></td>
                        <td><input type="number" name="details[{{ $index }}][cost_price]" value="{{ $detail->cost_price }}" class="form-control cost_price" readonly style="text-align: right;"></td>
                        <td><input type="number" name="details[{{ $index }}][quantity]" value="{{ $detail->quantity }}" class="form-control quantity" min="1" required style="text-align: right;"  ></td>
                        <td><input type="number" name="details[{{ $index }}][price]" value="{{ $detail->price }}" class="form-control price" readonly style="text-align: right;"></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary" onclick="addItem()">Add Item</button>

        <hr>

        <div class="mt-4" style="max-width: 400px; margin-left:auto;">
            <div class="mb-3 d-flex align-items-center">
                <label for="total_price" class="form-label me-2" style="width: 120px;">Total Price:</label>
                <input type="text" name="total_price" id="total_price" class="form-control" value="{{ $bill->total_price }}" readonly style="text-align: right;">
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="discount" class="form-label me-2" style="width: 120px;">Discount:</label>
                <input type="number" name="total_discount" id="total_discount" class="form-control" value="{{ $bill->total_discount }}" step="0.01" style="text-align: right;">
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="tobe_price" class="form-label me-2" style="width: 120px;">To Be Paid:</label>
                <input type="number" name="tobe_price" id="tobe_price" class="form-control" value="{{ $bill->tobe_price }}" step="0.01" readonly style="text-align: right;">
            </div>

            

            <div class="mb-3 d-flex align-items-center">
                <label for="supplier_pay" class="form-label me-2" style="width: 120px;">Customer Pay:</label>
                <input type="number" name="supplier_pay" id="supplier_pay" class="form-control" value="{{ $bill->supplier_pay }}" step="0.01" style="text-align: right;">
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="balance" class="form-label me-2" style="width: 120px;">Balance:</label>
                <input type="number" name="balance" id="balance" class="form-control" value="{{ $bill->balance }}" step="0.01" readonly style="text-align: right;">
            </div>

           
        </div>

        <button type="submit" class="btn btn-primary mt-3 float-end">Save Bill</button>
    </form>
</div>
<script>
    let allItems = [];

    // Fetch all items once on page load
    fetch('{{ route("items.ajax") }}')
        .then(res => res.json())
        .then(data => {
            allItems = data;
        });

    function removeRow(btn) {
        const row = btn.closest('tr');
        row.remove();
        calculateTotals();
    }

    function addItem() {
        const tbody = document.querySelector('#itemsTable tbody');
        const rows = tbody.querySelectorAll('tr.item-row');
        const newIndex = rows.length;

        const templateRow = rows[0].cloneNode(true);

        templateRow.querySelectorAll('input, select').forEach(input => {
            const name = input.name;
            if (!name) return;

            const newName = name.replace(/\[\d+\]/, `[${newIndex}]`);
            input.name = newName;
            input.value = '';

            // Make fields editable except price
            if (!input.classList.contains('price') && !input.classList.contains('rate') && !input.classList.contains('cost_price')) {
                input.removeAttribute('readonly');
            }

            if (input.classList.contains('price')) {
                input.readOnly = true;
            }
        });

        // Populate item select dropdown
        const select = templateRow.querySelector('.item-select');
        select.innerHTML = `<option value="">-- Select Item --</option>`;
        allItems.forEach(item => {
            const option = document.createElement('option');
            option.value = item.item_code;
            option.text = `${item.item_code} - ${item.item_name}`;
            select.appendChild(option);
        });

        tbody.appendChild(templateRow);
        calculateTotals();
    }

    function calculateTotals() {
        let total = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const cost_price = parseFloat(row.querySelector('.cost_price').value) || 0;
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = cost_price * qty;
            row.querySelector('.price').value = price.toFixed(2);
            total += price;
        });

        document.getElementById('total_price').value = total.toFixed(2);

        const discount = parseFloat(document.getElementById('total_discount').value) || 0;
        const tobe = total - discount;
        document.getElementById('tobe_price').value = tobe.toFixed(2);

        const supplierPay = parseFloat(document.getElementById('supplier_pay').value) || 0;
        const balance = supplierPay - tobe;
        document.getElementById('balance').value = balance.toFixed(2);
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select')) {
            const selectedCode = e.target.value;
            const row = e.target.closest('tr');
            const item = allItems.find(i => i.item_code === selectedCode);

            if (item) {
                row.querySelector('input[name$="[item_name]"]').value = item.item_name;
                row.querySelector('input[name$="[rate]"]').value = item.rate;
                row.querySelector('input[name$="[cost_price]"]').value = item.cost_price;
                calculateTotals();
            }
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity') || e.target.id === 'total_discount' || e.target.id === 'supplier_pay') {
            calculateTotals();
        }
    });

    window.addEventListener('load', calculateTotals);
</script>
@endsection
