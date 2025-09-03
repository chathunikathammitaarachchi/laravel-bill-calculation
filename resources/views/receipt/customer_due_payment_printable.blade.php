<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .receipt-box {
            max-width: 600px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,.15);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .line {
            border-bottom: 1px dashed #ccc;
            margin: 15px 0;
        }
        .label {
            font-weight: bold;
        }


    @media print {
        .no-print {
            display: none !important;
        }
    }

    </style>
</head>
<body>
    <div class="receipt-box">
        <h2>Customer Payment Receipt</h2>
        <div><span class="label">Customer:</span> {{ $customer_name }}</div>
        <div><span class="label">Payment Method:</span> {{ $payment_method }}</div>
        <div><span class="label">Amount Paid:</span> Rs. {{ number_format($amount, 2) }}</div>
        <div><span class="label">Payment Date:</span> {{ $payment_date }}</div>

        @if($payment_method === 'Cheque')
            <div><span class="label">Cheque No:</span> {{ $cheque_number }}</div>
            <div><span class="label">Bank:</span> {{ $bank_name }}</div>
            <div><span class="label">Branch:</span> {{ $branch_name }}</div>
            <div><span class="label">Cheque Date:</span> {{ $cheque_date }}</div>
        @endif

        <div class="line"></div>
        <div><span class="label">Total Due:</span> Rs. {{ number_format($totalDue, 2) }}</div>
        <div><span class="label">Total Paid:</span> Rs. {{ number_format($totalPaid, 2) }}</div>
        <div><span class="label">Remaining Balance:</span> Rs. {{ number_format($totalBalance, 2) }}</div>

        <div class="line"></div>
        <div style="text-align:center; margin-top: 30px;">Thank you for your payment!</div>
    </div>
<div class="no-print" style="text-align: center; margin-top: 30px;">
    <a href="{{ route('grn.dues.by.customer', ['customer_name' => $customer_name]) }}" class="btn btn-primary">Back to Dues</a>
</div>


    {{-- 🖨️ Trigger print after load --}}
    <script>
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 300); 
        };
    </script>
</body>
</html>
