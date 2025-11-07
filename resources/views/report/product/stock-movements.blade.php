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
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .in {
            background-color: #71dd37;
            color: #fff;
        }

        .out {
            background-color: #ff3e1d;
            color: #fff;
        }

        .adj {
            background-color: #ffab00;
            color: #fff;
        }

        .footer {
            margin-top: 30px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Stock Movement Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Product Name</th>
                <th>Type</th>
                <th class="text-right">Reference</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                [$total_in, $total_out, $total_restock] = [0, 0, 0];
            @endphp
            @foreach ($data['stockMovement'] as $stock)
                @php
                    $stat = $stock->orderCode == null ? 'adj' : strtolower(statusTransaction($stock->orderCode));
                    [$total_in, $total_out, $total_restock] =
                        $stat == 'in'
                            ? [($total_in += $stock->quantity), ($total_out += 0), ($total_restock += 0)]
                            : ($stat == 'out'
                                ? [($total_in += 0), ($total_out += $stock->quantity), ($total_restock += 0)]
                                : [($total_in += 0), ($total_out += 0), ($total_restock += $stock->quantity)]);
                @endphp
                <tr>
                    <td>{{ $stock->created_at }}</td>
                    <td>{{ $stock->orderCode ?? lastCompanyOrderCode('STOCKTAKING', $stock->created_at) }}</td>
                    <td>{{ $stock->name }}</td>
                    <td class="{{ $stat }}">{{ strtoupper($stat) }}</td>
                    <td class="text-right">{{ $stock->stock_reference }}</td>
                    <td class="text-right">{{ $stock->quantity }}</td>
                    <td class="text-right">{{ $stock->balance }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Total In: {{ $total_in }} | Total Out: {{ $total_out }} | Net Change: {{ $total_restock }}</p>

</body>

</html>
