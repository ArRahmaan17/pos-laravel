<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt {{ $orderCode ?? 'Empty' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 385px;
            padding: 0;
            margin-left: -40px;
            margin-top: -60px;
        }

        h2,
        p {
            text-align: center;
        }

        .address {
            width: 100%;
            font-size: 12px;
            text-align: center;
        }

        .details,
        .totals {
            margin-bottom: 10px;
        }

        .details>div {
            font-size: 12px;
        }

        .totals>div {
            font-size: 12px;
            text-align: right;
        }

        .thankyou {
            font-size: 13px;
            text-align: center;
            margin-top: 20px;
        }

        .item {
            height: 20px;
            margin-bottom: 10px;
        }

        .container-subtotal {
            margin-top: -5px;
            font-size: 12px;
        }

        .container-product {
            display: block;
        }

        .product {
            float: left;
            font-size: 15px;
        }

        .container-subtotal {
            float: right;
            font-size: 10px;
        }

        .subtotal {
            display: block;
            text-align: end;
        }
    </style>
</head>

<body>
    <h2>{{ session('userLogged')['company']['name'] }}</h2>
    <div class="address">
        {{ session('userLogged')['company']['address']['place'] . ',' . session('userLogged')['company']['address']['address'] . ',' . session('userLogged')['company']['address']['city'] . ',' . session('userLogged')['company']['address']['province'] . ',' . session('userLogged')['company']['address']['zipCode'] }}
    </div>
    <div class="address">
        {{ session('userLogged')['company']['email'] }},{{ session('userLogged')['company']['phone_number'] }}
    </div>
    <hr>

    <div class="details">
        <div>No. Struk: {{ $orderCode ?? 'Empty' }}</div>
        <div>Tanggal: {{ !empty($created_at) ? explode(' ', new Carbon\Carbon($created_at))[0] : 'Empty' }}</div>
        <div>Waktu: {{ !empty($created_at) ? explode(' ', new Carbon\Carbon($created_at))[1] : 'Empty' }}</div>
    </div>
    <hr>

    <div class="items">
        @if (!empty($details))
            @foreach ($details as $detail)
                <div class="item">
                    <div class="container-product">
                        <div class="product">{{ $detail['good']['name'] }}</div>
                        <div class="container-subtotal">
                            <div class="price">{{ numberFormat($detail['good']['price']) }} * {{ $detail['quantity'] }}
                            </div>
                            <div class="subtotal">{{ numberFormat($detail['good']['price'] * $detail['quantity']) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            Empty
        @endif
    </div>
    <hr>

    <div class="totals">
        <div><strong>Discount: {{ numberFormat($discount ?? 0) }}</strong></div>
        <div><strong>Total: {{ numberFormat(!empty($total) ? $total - $discount : 0) }}</strong></div>
    </div>

    <div class="thankyou">Terima kasih telah berbelanja!</div>

</body>

</html>
