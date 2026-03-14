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
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .section-title {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .footer {
            font-weight: bold;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <h1>Income vs Expense Report</h1>
    <h2>{{ $customer_company }}</h2>

    <p><strong>Generated On:</strong> {{ $generated_date }}</p>
    <p><strong>Period:</strong> {{ $report_period }}</p>

    <!-- Income Section -->
    <h3>Income</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Reference</th>
                <th class="text-right">Amount (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_income = 0;
            @endphp
            @foreach ($data['incomeVsExpense'] as $flow)
                @if (statusTransaction($flow->orderCode) === 'OUT')
                    <tr>
                        <td>{{ $flow->created_at }}</td>
                        <td>Description</td>
                        <td>{{ $flow->orderCode }}</td>
                        <td class="text-right">{{ numberFormat($flow->amount) }}</td>
                    </tr>
                    @php
                        $total_income += $flow->amount;
                    @endphp
                @endif
            @endforeach
            <tr class="section-title">
                <td colspan="3">Total Inflows</td>
                <td class="text-right">Rp {{ numberFormat($total_income) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Expense Section -->
    <h3>Expenses</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Reference</th>
                <th class="text-right">Amount (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_expense = 0;
            @endphp
            @foreach ($data['incomeVsExpense'] as $flow)
                @if (statusTransaction($flow->orderCode) === 'IN')
                    <tr>
                        <td>{{ $flow->created_at }}</td>
                        <td>Description</td>
                        <td>{{ $flow->orderCode }}</td>
                        <td class="text-right">{{ numberFormat($flow->amount) }}</td>
                    </tr>
                    @php
                        $total_expense += $flow->amount;
                    @endphp
                @endif
            @endforeach
            <tr class="section-title">
                <td colspan="3">Total Outflows</td>
                <td class="text-right">Rp {{ numberFormat($total_expense) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary -->
    <h3>Summary</h3>
    <table>
        <tbody>
            <tr>
                <td><strong>Total Income</strong></td>
                <td class="text-right">Rp {{ numberFormat($total_income) }}</td>
            </tr>
            <tr>
                <td><strong>Total Expenses</strong></td>
                <td class="text-right">Rp {{ numberFormat($total_expense) }}</td>
            </tr>
            <tr>
                <td><strong>Net Profit / Loss</strong></td>
                <td class="text-right"><strong>Rp {{ numberFormat($total_income - $total_expense) }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
