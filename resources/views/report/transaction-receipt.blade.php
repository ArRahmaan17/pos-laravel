<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Kasir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 385px;
            padding: 0;
            margin-left: -40px;
            margin-top: -70px;
        }

        h2,
        p,
        .address {
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
    <h2>Nama Toko</h2>
    <div class="address">Alamat Toko, Nomor Telepon</div>
    <hr>

    <div class="details">
        <div>No. Struk: 001234</div>
        <div>Tanggal: 20 Sept 2024</div>
        <div>Waktu: 14:35</div>
    </div>
    <hr>

    <div class="items">
        @for ($i = 1; $i <= 3; $i++)
            <div class="item">
                <div class="container-product">
                    <div class="product">Produk A</div>
                    <div class="container-subtotal">
                        <div class="price">10,000 * 2</div>
                        <div class="subtotal">20,000</div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    <hr>

    <div class="totals">
        <div><strong>Total: 38,500</strong></div>
    </div>

    <div class="thankyou">Terima kasih telah berbelanja!</div>

</body>

</html>
