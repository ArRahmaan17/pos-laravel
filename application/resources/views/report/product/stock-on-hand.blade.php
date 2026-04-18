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

        .section-title {
            margin-top: 30px;
            font-weight: bold;
            font-size: 14px;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <h1>Stock On Hand Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <table>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Weight</th>
            <th class="text-right">Stock</th>
            <th class="text-right">Cost / Unit (Rp)</th>
            <th class="text-right">Total Value (Rp)</th>
        </tr>
        @php
            $grant_total = 0;
        @endphp
        @foreach ($data['stockOnHand'] as $stock)
            <tr>
                <td>{{ $stock->name }}</td>
                <td>{{ $stock->category }}</td>
                <td>{{ $stock->unit }}</td>
                <td class="text-right">{{ numberFormat($stock->stock) }}</td>
                <td class="text-right">{{ numberFormat($stock->price) }}</td>
                <td class="text-right">{{ numberFormat($stock->total_value) }}</td>
            </tr>
            @php
                $grant_total += $stock->total_value;
            @endphp
        @endforeach
    </table>

    <p><strong>Total Stock Value:</strong> Rp {{ numberFormat($grant_total) }}</p>

</body>

</html>
