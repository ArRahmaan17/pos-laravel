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
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 30px;
        }

        .text-right {
            text-align: right;
        }

        .fast {
            background-color: #e0ffe0;
        }

        /* Hijau muda */
        .slow {
            background-color: #ffe0e0;
        }

        /* Merah muda */
    </style>
</head>

<body>

    <h1>Product Performance Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <div class="section-title">Fast Moving Products</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Product Code</th>
                <th>Category</th>
                <th class="text-right">Sold Qty</th>
                <th class="text-right">Sales (Rp)</th>
                <th class="text-right">Average Daily Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['productPerformance']['fastMoving'] as $fastMove)
                <tr>
                    <td>{{ $fastMove->row_numbers }}</td>
                    <td>{{ $fastMove->product_name }}</td>
                    <td>{{ $fastMove->category }}</td>
                    <td class="text-right">{{ $fastMove->sold }}</td>
                    <td class="text-right">{{  numberFormat($fastMove->sales) }}</td>
                    <td class="text-right">{{ $fastMove->sales_per_day }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Slow Moving Products</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Product Name</th>
                <th>Category</th>
                <th class="text-right">Sold Qty</th>
                <th class="text-right">Sales (Rp)</th>
                <th class="text-right">Days Without Sale</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['productPerformance']['slowMoving'] as $slowMove)
                <tr>
                    <td>{{ $slowMove->row_numbers }}</td>
                    <td>{{ $slowMove->product_name }}</td>
                    <td>{{ $slowMove->category }}</td>
                    <td class="text-right">{{ $slowMove->sold }}</td>
                    <td class="text-right">{{ numberFormat($slowMove->sales) }}</td>
                    <td class="text-right">{{ $slowMove->sales_per_day }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
