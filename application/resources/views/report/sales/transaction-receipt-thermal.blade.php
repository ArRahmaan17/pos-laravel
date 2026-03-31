<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Receipt {{ $orderCode ?? 'Empty' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Courier New", monospace;
            font-size: 12px;
            line-height: 1.35;
            width: 80mm;
            margin: 0 auto;
            padding: 8px 6px 16px;
            color: #111;
            background: #fff;
        }

        h1,
        p {
            margin: 0;
            text-align: center;
        }

        .muted {
            color: #555;
            font-size: 11px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
        }

        .items {
            margin-top: 8px;
        }

        .item {
            padding: 6px 0;
            border-bottom: 1px dotted #bbb;
        }

        .item:last-child {
            border-bottom: 0;
        }

        .item-name {
            font-weight: 700;
        }

        .item-meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            color: #555;
            font-size: 11px;
        }

        .totals {
            margin-top: 10px;
        }

        .thanks {
            margin-top: 14px;
            text-align: center;
            font-weight: 700;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                width: auto;
                margin: 0;
                padding: 6mm 4mm 8mm;
            }
        }
    </style>
</head>

<body>
    <h1>{{ session('userLogged')['company']['name'] }}</h1>
    <p class="muted">
        {{ session('userLogged')['company']['address']['place'] . ', ' . session('userLogged')['company']['address']['address'] }}
    </p>
    <p class="muted">
        {{ session('userLogged')['company']['address']['city'] . ', ' . session('userLogged')['company']['address']['province'] . ' ' . session('userLogged')['company']['address']['zip_code'] }}
    </p>
    <p class="muted">{{ session('userLogged')['company']['phone_number'] }}</p>

    <div class="divider"></div>

    <div class="row">
        <div>Receipt</div>
        <div>{{ $orderCode ?? 'Empty' }}</div>
    </div>
    <div class="row">
        <div>Date</div>
        <div>{{ !empty($created_at) ? \Carbon\Carbon::parse($created_at)->format('Y-m-d H:i:s') : 'Empty' }}</div>
    </div>

    <div class="divider"></div>

    <div class="items">
        @forelse ($details ?? [] as $detail)
            <div class="item">
                <div class="item-name">{{ $detail['good']['name'] }}</div>
                <div class="item-meta">
                    <span>{{ numberFormat($detail['good']['price']) }} x {{ $detail['quantity'] }}</span>
                    <span>{{ numberFormat($detail['good']['price'] * $detail['quantity']) }}</span>
                </div>
            </div>
        @empty
            <div class="item">Empty</div>
        @endforelse
    </div>

    <div class="divider"></div>

    <div class="totals">
        <div class="row">
            <div>Subtotal</div>
            <div>{{ numberFormat(($total ?? 0)) }}</div>
        </div>
        <div class="row">
            <div>Discount</div>
            <div>{{ numberFormat($discount ?? 0) }}</div>
        </div>
        <div class="row">
            <div><strong>Total</strong></div>
            <div><strong>{{ numberFormat(($total ?? 0) - ($discount ?? 0)) }}</strong></div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="thanks">Thank you for shopping</div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>
