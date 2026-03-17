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

    <h1>Sales Summary Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <h3>Overview</h3>
    <table>
        <tr>
            <th>Metric</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Total Sales</td>
            <td>Rp {{ numberFormat($data['salesOverview']->total_sales) }}</td>
        </tr>
        <tr>
            <td>Total Orders</td>
            <td>{{ $data['salesOverview']->total_orders }}</td>
        </tr>
        <tr>
            <td>Average Order Value</td>
            <td>Rp {{ numberFormat($data['salesOverview']->avg_order_value) }}</td>
        </tr>
    </table>

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
        </tr>
    </table>

    <h3>Top 5 Products</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Units Sold</th>
            <th>Total Revenue</th>
            <th>Average Price</th>
        </tr>
        @foreach ($data['salesTopProduct'] as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->total_quantity }}</td>
                <td>Rp {{ numberFormat($product->total_amount) }}</td>
                <td>Rp {{ numberFormat($product->average_amount) }}</td>
            </tr>
        @endforeach
    </table>

</body>

</html>
