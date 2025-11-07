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

    <h1>Sales by Product Category Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <h3>Sales by Category</h3>
    <table>
        <tr>
            <th>Category</th>
            <th>Total Sales</th>
            <th>Orders</th>
            <th>Top Product</th>
        </tr>
        @foreach ($data['salesByCategory'] as $category)
            <tr>
                <td>{{ $category->category }}</td>
                <td>{{ $category->total_quantity }}</td>
                <td>{{ $category->total_orders }}</td>
                <td>{{ $category->top_product }}</td>
        @endforeach
    </table>
</body>

</html>
