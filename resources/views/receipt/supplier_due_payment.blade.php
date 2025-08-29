<!DOCTYPE html>
<html>
<head>
    <title>Supplier Due Payment Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 40px;
            color: #333;
            background-color: #f9f9f9;
        }
        .receipt-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px 40px;
            border: 1px solid #ddd;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0;
            color: #4CAF50;
            font-weight: 700;
            letter-spacing: 1.2px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            color: #666;
        }
        .details p {
            margin: 12px 0;
            font-size: 1rem;
        }
        .details p span.label {
            display: inline-block;
            width: 140px;
            font-weight: 600;
            color: #555;
        }
        .amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2E7D32; /* Dark Green */
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 35px;
            font-size: 0.85rem;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
            letter-spacing: 0.03em;
        }
        @media print {
            body {
                background-color: white;
                margin: 0;
            }
            .receipt-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h2>Payment Receipt</h2>
            <p>Supplier: {{ $supplier_name }}</p>
            <p>Date: {{ $payment_date }}</p>
        </div>

        <div class="details">
            <p><span class="label">Payment Method:</span> {{ $payment_method }}</p>

            @if($payment_method === 'Cheque')
                <p><span class="label">Cheque Number:</span> {{ $cheque_number }}</p>
                <p><span class="label">Bank:</span> {{ $bank_name }}</p>
                <p><span class="label">Branch:</span> {{ $branch_name }}</p>
                <p><span class="label">Cheque Date:</span> {{ $cheque_date }}</p>
            @endif

            <p class="amount">Amount Paid: Rs. {{ number_format($amount, 2) }}</p>
        </div>

        <div class="footer">
            <p>Thank you for your payment.</p>
            <p>If you have any questions, please contact our support.</p>
        </div>
    </div>
</body>
</html>
