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
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <h1>Sales by Product Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>
    <h3>Sales by Category</h3>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th class="text-right">Quantity Sold</th>
                <th class="text-right">Unit Price (Rp)</th>
                <th class="text-right">Total Sales (Rp)</th>
            </tr>
        </thead>
        @php
            $grant_total = 0;
        @endphp
        <tbody>
            @foreach ($data['salesProduct'] as $product)
                @php
                    $grant_total += $product->total_revenue;
                @endphp
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->category }}</td>
                    <td class="text-right">{{ $product->total_quantity }}</td>
                    <td class="text-right">{{ numberFormat($product->price) }}</td>
                    <td class="text-right">{{ numberFormat($product->total_revenue) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-right"><strong>Total Sales</strong></td>
                <td class="text-right"><strong>Rp {{ numberFormat($grant_total) }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
