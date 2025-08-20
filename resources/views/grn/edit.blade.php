@extends('layouts.app')

@section('content')
<div class="container mt-4" style="max-width: 1200px;">

    <div style="background: linear-gradient(135deg, #e6f3faff 0%, #b8cee7ff 50%, #c4abf3ff 100%);

 padding: 80px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <h2 class="mb-4" style="border-bottom: 2px solid #007bff; padding-bottom: 10px; font-weight: bold; color: #343a40;">
            Edit Bill - Bill No: {{ $grn->bill_no }}
        </h2>

        <form action="{{ route('grn.update', $grn->bill_no) }}" method="POST" id="grnForm">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="bill_no" class="form-label fw-bold">Bill No</label>
                    <input type="text" name="bill_no" id="bill_no" class="form-control" value="{{ $grn->bill_no }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="grn_date" class="form-label fw-bold">Date</label>
                    <input type="date" name="grn_date" id="grn_date" class="form-control" value="{{ old('grn_date', \Carbon\Carbon::parse($grn->grn_date)->format('Y-m-d')) }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="customer_name" class="form-label fw-bold">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ $grn->customer_name }}" readonly>
                </div>
            </div>

            <hr class="my-4">

            <h4 style="font-weight: 600; color: #495057;">Items</h4>
            <div class="table-responsive">
                <table class="table table-bordered mt-3" id="itemsTable" style="background-color: #fafafa;">
                    <thead class="table-dark">
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th style="text-align: right;">Rate</th>
                            <th style="text-align: right;">Quantity</th>
                            <th style="text-align: right;">Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grn->details as $index => $detail)
                            <tr class="item-row align-middle">
                                <td><input type="text" name="details[{{ $index }}][item_code]" value="{{ $detail->item_code }}" class="form-control" readonly></td>
                                <td><input type="text" name="details[{{ $index }}][item_name]" value="{{ $detail->item_name }}" class="form-control" readonly></td>
                                <td><input type="number" name="details[{{ $index }}][rate]" value="{{ $detail->rate }}" class="form-control rate text-end" readonly></td>
                                <td><input type="number" name="details[{{ $index }}][quantity]" value="{{ $detail->quantity }}" class="form-control quantity text-end" min="1" required></td>
                                <td><input type="number" name="details[{{ $index }}][price]" value="{{ $detail->price }}" class="form-control price text-end" readonly></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-secondary my-2" onclick="addItem()">+ Add Item</button>

            <hr class="my-4">

            <div style="max-width: 400px; margin-left: auto;">
                <div class="mb-3 d-flex align-items-center">
                    <label for="total_price" class="form-label me-2 fw-bold" style="width: 150px;">Total Price:</label>
                    <input type="text" name="total_price" id="total_price" class="form-control text-end" value="{{ $grn->total_price }}" readonly>
                </div>

                <div class="mb-3 d-flex align-items-center">
                    <label for="total_discount" class="form-label me-2 fw-bold" style="width: 150px;">Discount:</label>
                    <input type="number" name="total_discount" id="total_discount" class="form-control text-end" value="{{ $grn->total_discount }}" step="0.01">
                </div>

                <div class="mb-3 d-flex align-items-center">
                    <label for="tobe_price" class="form-label me-2 fw-bold" style="width: 150px;">To Be Paid:</label>
                    <input type="number" name="tobe_price" id="tobe_price" class="form-control text-end" value="{{ $grn->tobe_price }}" readonly>
                </div>

                <div class="mb-3 d-flex align-items-center">
                    <label for="customer_pay" class="form-label me-2 fw-bold" style="width: 150px;">Customer Pay:</label>
                    <input type="number" name="customer_pay" id="customer_pay" class="form-control text-end" value="{{ $grn->customer_pay }}" step="0.01">
                </div>

                <div class="mb-3 d-flex align-items-center">
                    <label for="balance" class="form-label me-2 fw-bold" style="width: 150px;">Balance:</label>
                    <input type="number" name="balance" id="balance" class="form-control text-end" value="{{ $grn->balance }}" readonly>
                </div>

                <div class="mb-3 d-flex align-items-center">
                    <label for="received_by" class="form-label me-2 fw-bold" style="width: 150px;">Received By:</label>
                    <input type="text" name="received_by" id="received_by" class="form-control" value="{{ $grn->received_by }}" required>
                </div>

                <div class="mb-3 d-flex align-items-center">
                    <label for="issued_by" class="form-label me-2 fw-bold" style="width: 150px;">Issued By:</label>
                    <input type="text" name="issued_by" id="issued_by" class="form-control" value="{{ $grn->issued_by }}" required>
                </div>

                <button type="submit" class="btn btn-primary mt-3 float-end">💾 Save Bill</button>
            </div>
        </form>
    </div>
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
        calculateTotals();
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
