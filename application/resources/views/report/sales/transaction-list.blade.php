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
            margin-bottom: 20px;
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

        .text-center {
            text-align: center;
        }

        .footer {
            font-weight: bold;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <h1>Complete Transaction List</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Invoice No.</th>
                <th>Items</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Total (Rp)</th>
                <th>Cashier / Staff</th>
            </tr>
        </thead>
        <tbody>
            @php
                [$transactionCount, $revenue] = [0, 0];
            @endphp
            @foreach ($data['transactionComplete'] as $complete)
                <tr>
                    <td>{{ $complete->row_numbers }}</td>
                    <td>{{ $complete->transaction_create }}</td>
                    <td>{{ $complete->orderCode }}</td>
                    <td>{{ $complete->product_quantity }}</td>
                    <td class="text-right">{{ $complete->quantity }}</td>
                    <td class="text-right">{{ numberFormat($complete->price) }}</td>
                    <td>{{ $complete->name }}</td>
                </tr>
                @php
                    [$transactionCount, $revenue] = [($transactionCount += 1), ($revenue += $complete->price)];
                @endphp
            @endforeach
        </tbody>
    </table>

    <p class="footer">
        Total Transactions: {{ $transactionCount }}<br>
        Total Revenue: Rp {{ numberFormat($revenue) }}
    </p>

</body>

</html>
