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
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .section-title {
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <h1>Sales by Cashier / Staff</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <table>
        <tr>
            <th>Staff Name</th>
            <th>Total Orders</th>
            <th>Total Sales (Rp)</th>
            <th>Average Order (Rp)</th>
        </tr>
        @foreach ($data['salesByStaff'] as $sales)
            <tr>
                <td>{{ $sales->staff_name }}</td>
                <td>{{ $sales->total_orders }}</td>
                <td>{{ numberFormat($sales->total_sales) }}</td>
                <td>{{ numberFormat($sales->avg_order_value) }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>
