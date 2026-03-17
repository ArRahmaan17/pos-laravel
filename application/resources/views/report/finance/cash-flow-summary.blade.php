<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h1,
        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .section-title {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .footer {
            font-weight: bold;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <h1>Cash Flow Summary</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <!-- Cash Inflows -->
    <h3>Cash Inflows</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Reference</th>
                <th class="text-right">Amount (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_cash_in = 0;
            @endphp
            @foreach ($data['cashFlow'] as $flow)
                @if (statusTransaction($flow->orderCode) === 'OUT')
                    <tr>
                        <td>{{ $flow->created_at }}</td>
                        <td>Description</td>
                        <td>{{ $flow->orderCode }}</td>
                        <td class="text-right">{{ numberFormat($flow->amount) }}</td>
                    </tr>
                    @php
                        $total_cash_in += $flow->amount;
                    @endphp
                @endif
            @endforeach
            <tr class="section-title">
                <td colspan="3">Total Inflows</td>
                <td class="text-right">Rp {{ numberFormat($total_cash_in) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Cash Outflows -->
    <h3>Cash Outflows</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Reference</th>
                <th class="text-right">Amount (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_cash_out = 0;
            @endphp
            @foreach ($data['cashFlow'] as $flow)
                @if (statusTransaction($flow->orderCode) === 'IN')
                    <tr>
                        <td>{{ $flow->created_at }}</td>
                        <td>Description</td>
                        <td>{{ $flow->orderCode }}</td>
                        <td class="text-right">{{ numberFormat($flow->amount) }}</td>
                    </tr>
                    @php
                        $total_cash_out += $flow->amount;
                    @endphp
                @endif
            @endforeach
            <tr class="section-title">
                <td colspan="3">Total Outflows</td>
                <td class="text-right">Rp {{ numberFormat($total_cash_out) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary -->
    <h3>Summary</h3>
    <table>
        <tbody>
            <tr>
                <td><strong>Opening Balance</strong></td>
                <td class="text-right">Rp {{ numberFormat($total_cash_out) }}</td>
            </tr>
            <tr>
                <td><strong>Total Cash In</strong></td>
                <td class="text-right">Rp {{ numberFormat($total_cash_in) }}</td>
            </tr>
            <tr>
                <td><strong>Total Cash Out</strong></td>
                <td class="text-right">Rp {{ numberFormat($total_cash_out) }}</td>
            </tr>
            <tr>
                <td><strong>Closing Balance</strong></td>
                <td class="text-right"><strong>Rp {{ numberFormat($total_cash_out + $total_cash_in + (-$total_cash_out)) }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
