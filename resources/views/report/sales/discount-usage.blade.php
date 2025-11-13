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
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Discount Usage Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Invoice No.</th>
                <th>Discount CODE</th>
                <th>Description</th>
                <th class="text-right">Discount Amount (Rp)</th>
                <th class="text-right">Original Total</th>
                <th class="text-right">Final Total</th>
                <th>Approved By</th>
            </tr>
        </thead>
        <tbody>
            @php
                [$total_discounts, $total_transactions] = [0, 0];
            @endphp
            @foreach ($data['discountUsage'] as $discount)
                <tr>
                    <td>{{ $discount->row_numbers }}</td>
                    <td>{{ $discount->transaction_create }}</td>
                    <td>{{ $discount->orderCode }}</td>
                    <td>{{ $discount->code }}</td>
                    <td>{{ $discount->description }}</td>
                    <td class="text-right">{{ numberFormat($discount->discount) }}</td>
                    <td class="text-right">{{ numberFormat($discount->total) }}</td>
                    <td class="text-right">{{ numberFormat($discount->total_after_discount) }}</td>
                    <td>{{ $discount->name }}</td>
                </tr>
                @php
                    [$total_discounts, $total_transactions] = [($total_discounts += $discount->discount), $total_transactions+=1];
                @endphp
            @endforeach
        </tbody>
    </table>

    <p class="footer">
        Total Discounts Given: Rp {{ numberFormat($total_discounts) }}<br>
        Affected Transactions: {{ $total_transactions }}
    </p>

</body>

</html>
