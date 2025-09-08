@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1000px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px;">
    <h4 class="text-center mb-3">Ledger for Supplier: {{ $supplierName }}</h4>
    
    @if($startDate && $endDate)
        <p class="text-center">From <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong></p>
    @endif

    <table class="table table-bordered mt-4">
        <thead class="table-primary">
            <tr>
                <th>Date</th>
                <th>Bill No / Cheque No</th>
                <th>Description</th>
                <th class="text-end">Paid (Dr)</th>
                <th class="text-end">To be Paid (Cr)</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
        @php
            $totalPaid = 0;
            $totalToBePaid = 0;
            $balance = $openingBalance ?? 0;
        @endphp

        @foreach($ledger as $entry)
            @php
                $paid = is_numeric($entry['debit']) ? $entry['debit'] : 0;
                $credit = is_numeric($entry['credit']) ? $entry['credit'] : 0;

                // Only add positive payments to totalPaid
                if ($paid > 0) {
                    $totalPaid += $paid;
                }

                $totalToBePaid += $credit;
                $balance = $entry['balance'];
                
                // Determine row class based on entry type
                $rowClass = '';
                if (strpos($entry['description'], 'Cheque Return') !== false) {
                    $rowClass = 'table-warning'; // Yellow for cheque returns
                } elseif (strpos($entry['description'], 'Opening Balance') !== false) {
                    $rowClass = 'table-info'; // Blue for opening balance
                } elseif (strpos($entry['description'], 'Payment') !== false) {
                    $rowClass = 'table-success'; // Green for payments
                }
            @endphp
            <tr class="{{ $rowClass }}">
                <td>{{ $entry['date'] ? \Carbon\Carbon::parse($entry['date'])->format('Y-m-d') : '' }}</td>
                <td>{{ $entry['bill_no'] }}</td>
                <td>
                    {{ $entry['description'] }}
                    @if (strpos($entry['description'], 'Cheque Return') !== false)
                        <small class="text-danger d-block">⚠️ Returned Cheque</small>
                    @endif
                </td>
                <td class="text-end">
                    @if($paid != 0)
                        @if($paid < 0)
                            <span class="text-danger">({{ number_format(abs($paid), 2, '.', ',') }})</span>
                        @else
                            {{ number_format($paid, 2, '.', ',') }}
                        @endif
                    @endif
                </td>
                <td class="text-end">{{ $credit != 0 ? number_format($credit, 2, '.', ',') : '' }}</td>
                <td class="text-end">
                    @if($balance < 0)
                        <span class="text-danger">{{ number_format($balance, 2, '.', ',') }}</span>
                    @else
                        <span class="text-success">{{ number_format($balance, 2, '.', ',') }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
            <tr class="fw-bold table-dark">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">{{ number_format($totalPaid, 2, '.', ',') }}</td>
                <td class="text-end">{{ number_format($totalToBePaid, 2, '.', ',') }}</td>
                <td class="text-end">
                    @php $finalBalance = $totalToBePaid - $totalPaid; @endphp
                    @if($finalBalance < 0)
                        <span class="text-danger">{{ number_format($finalBalance, 2, '.', ',') }}</span>
                    @else
                        <span class="text-success">{{ number_format($finalBalance, 2, '.', ',') }}</span>
                    @endif
                </td>
            </tr>
            <tr class="fw-bold bg-light">
                <td colspan="5" class="text-end">Final Outstanding Balance</td>
                <td class="text-end">
                    @if($finalBalance < 0)
                        <span class="text-danger">{{ number_format(abs($finalBalance), 2, '.', ',') }}</span>
                        <small class="d-block text-muted">Amount to be paid</small>
                    @elseif($finalBalance > 0)
                        <span class="text-success">{{ number_format($finalBalance, 2, '.', ',') }}</span>
                        <small class="d-block text-muted">Advance payment</small>
                    @else
                        <span class="text-info">0.00</span>
                        <small class="d-block text-muted">Account settled</small>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    
</div>
@endsection