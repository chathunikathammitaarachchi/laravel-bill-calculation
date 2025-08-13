@extends('layouts.app')

@section('content')
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
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->details as $index => $detail)
                    <tr class="item-row">
                        <td>
                            <input type="text" name="details[{{ $index }}][item_code]" value="{{ $detail->item_code }}" class="form-control" readonly>
                        </td>
                        <td><input type="text" name="details[{{ $index }}][item_name]" value="{{ $detail->item_name }}" class="form-control" readonly></td>
                        <td><input type="number" name="details[{{ $index }}][rate]" value="{{ $detail->rate }}" class="form-control rate" readonly style="text-align: right;"></td>
                        <td><input type="number" name="details[{{ $index }}][quantity]" value="{{ $detail->quantity }}" class="form-control quantity" min="1" required style="text-align: right;" readonly ></td>
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
    function removeRow(btn) {
        const row = btn.closest('tr');
        row.remove();
        calculateTotals();
    }

    function addItem() {
        const table = document.getElementById('itemsTable').querySelector('tbody');
        const lastRow = table.querySelector('tr:last-child');
        const newRow = lastRow.cloneNode(true);

        const newIndex = table.querySelectorAll('tr').length;

        newRow.querySelectorAll('input').forEach(input => {
            let name = input.getAttribute('name');
            if (name) {
                name = name.replace(/\[\d+\]/, `[${newIndex}]`);
                input.setAttribute('name', name);
            }

            if (input.classList.contains('quantity')) {
                input.value = 1;
            } else if (!input.readOnly) {
                input.value = '';
            }

            if (input.classList.contains('price')) {
                input.value = '0.00';
            }
        });

        table.appendChild(newRow);
    }

    function calculateTotals() {
        let total = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const rate = parseFloat(row.querySelector('.rate').value) || 0;
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = rate * qty;
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

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity') || e.target.id === 'total_discount' || e.target.id === 'supplier_pay') {
            calculateTotals();
        }
    });

    window.addEventListener('load', calculateTotals);
</script>



@endsection
