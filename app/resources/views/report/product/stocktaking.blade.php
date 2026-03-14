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
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .diff {
            background-color: #fff4e5;
        }

        .footer {
            margin-top: 30px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Stocktaking Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Product Name</th>
                <th>Unit</th>
                <th class="text-right">System Stock</th>
                <th class="text-right">Physical Stock</th>
                <th class="text-right">Difference</th>
                <th class="text-right">Cost/Unit</th>
                <th class="text-right">Value Difference</th>
            </tr>
        </thead>
        <tbody>
            @php
                [$total_gain, $total_loss, $net_adjustment] = [0, 0, 0];
            @endphp
            @foreach ($data['stockTaking'] as $stock)
                <tr>
                    <td>No</td>
                    <td>{{ $stock->name }}</td>
                    <td>{{ $stock->unit_name }}</td>
                    <td class="text-right">{{ $stock->system_stock }}</td>
                    <td class="text-right">{{ $stock->physical_stock }}</td>
                    <td class="text-right">{{ $stock->difference }}</td>
                    <td class="text-right">{{ numberFormat($stock->cost) }}</td>
                    <td class="text-right">{{ numberFormat($stock->value_diff) }}</td>
                </tr>
                @php
                    [$total_gain, $total_loss, $net_adjustment] =
                        $stock->system_stock < $stock->physical_stock
                            ? [
                                ($total_gain += 0),
                                ($total_loss += $stock->difference * $stock->cost),
                                ($net_adjustment += $stock->difference * $stock->cost),
                            ]
                            : [
                                ($total_gain += $stock->difference * $stock->cost),
                                ($total_loss += 0),
                                ($net_adjustment += $stock->difference * $stock->cost),
                            ];
                @endphp
            @endforeach
        </tbody>
    </table>

    <p class="footer">
        Total Positive Adjustment: Rp {{ numberFormat($total_gain) }}<br>
        Total Negative Adjustment: Rp {{ numberFormat($total_loss) }}<br>
        Net Value Adjustment: Rp {{ numberFormat($net_adjustment) }}
    </p>

</body>

</html>
