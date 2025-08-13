@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2>Edit Bill - Bill No: {{ $grn->bill_no }}</h2>
    <form action="{{ route('grn.update', $grn->bill_no) }}" method="POST" id="grnForm">
        @csrf
        @method('PUT')

        <div class="mb-3" style="max-width: 300px;">
            <label for="bill_no" class="form-label">Bill No</label>
            <input type="text" name="bill_no" id="bill_no" class="form-control" value="{{ $grn->bill_no }}" readonly>
        </div>

        <div class="mb-3" style="max-width: 300px;">
            <label for="grn_date" class="form-label">Date</label>
            <input type="date" name="grn_date" id="grn_date" class="form-control" value="{{ old('grn_date', \Carbon\Carbon::parse($grn->grn_date)->format('Y-m-d')) }}" readonly>
        </div>

        <div class="mb-3" style="max-width: 300px;">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ $grn->customer_name }}" readonly>
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
                @foreach($grn->details as $index => $detail)
                    <tr class="item-row">
                        <td>
                            <input type="text" name="details[{{ $index }}][item_code]" value="{{ $detail->item_code }}" class="form-control" readonly>
                        </td>
                        <td><input type="text" name="details[{{ $index }}][item_name]" value="{{ $detail->item_name }}" class="form-control" readonly></td>
                        <td><input type="number" name="details[{{ $index }}][rate]" value="{{ $detail->rate }}" class="form-control rate" readonly style="text-align: right;"></td>
                        <td><input type="number" name="details[{{ $index }}][quantity]" value="{{ $detail->quantity }}" class="form-control quantity" min="1" required style="text-align: right;"></td>
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
                <input type="text" name="total_price" id="total_price" class="form-control" value="{{ $grn->total_price }}" readonly style="text-align: right;">
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="discount" class="form-label me-2" style="width: 120px;">Discount:</label>
                <input type="number" name="total_discount" id="total_discount" class="form-control" value="{{ $grn->total_discount }}" step="0.01" style="text-align: right;">
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="tobe_price" class="form-label me-2" style="width: 120px;">To Be Paid:</label>
                <input type="number" name="tobe_price" id="tobe_price" class="form-control" value="{{ $grn->tobe_price }}" step="0.01" readonly style="text-align: right;">
            </div>

            

            <div class="mb-3 d-flex align-items-center">
                <label for="customer_pay" class="form-label me-2" style="width: 120px;">Customer Pay:</label>
                <input type="number" name="customer_pay" id="customer_pay" class="form-control" value="{{ $grn->customer_pay }}" step="0.01" style="text-align: right;">
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="balance" class="form-label me-2" style="width: 120px;">Balance:</label>
                <input type="number" name="balance" id="balance" class="form-control" value="{{ $grn->balance }}" step="0.01" readonly style="text-align: right;">
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="received_by" class="form-label me-2" style="width: 120px;">Received By:</label>
                <input type="text" name="received_by" id="received_by" class="form-control" value="{{ $grn->received_by }}" required>
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label for="issued_by" class="form-label me-2" style="width: 120px;">Issued By:</label>
                <input type="text" name="issued_by" id="issued_by" class="form-control" value="{{ $grn->issued_by }}" required>
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

        const customerPay = parseFloat(document.getElementById('customer_pay').value) || 0;
        const balance = customerPay - tobe;
        document.getElementById('balance').value = balance.toFixed(2);
    }

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity') || e.target.id === 'total_discount' || e.target.id === 'customer_pay') {
            calculateTotals();
        }
    });

    window.addEventListener('load', calculateTotals);
</script>
@endsection
