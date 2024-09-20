<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Kasir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 300px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }

        h2,
        p {
            text-align: center;
        }

        .details,
        .totals {
            margin-bottom: 15px;
        }

        .items {
            width: 100%;
        }

        .items th,
        .items td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .items th {
            background-color: #f2f2f2;
        }

        .totals p {
            text-align: right;
        }

        .thankyou {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <h2>Nama Toko</h2>
    <p>Alamat Toko, Nomor Telepon</p>
    <hr>

    <div class="details">
        <p>No. Struk: 001234</p>
        <p>Tanggal: 20 Sept 2024</p>
        <p>Waktu: 14:35</p>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Kuantitas</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Produk A</td>
                <td>2</td>
                <td>Rp10,000</td>
                <td>Rp20,000</td>
            </tr>
            <tr>
                <td>Produk B</td>
                <td>1</td>
                <td>Rp15,000</td>
                <td>Rp15,000</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <p>Subtotal: Rp35,000</p>
        <p>Pajak: Rp3,500</p>
        <p><strong>Total: Rp38,500</strong></p>
    </div>

    <p>Metode Pembayaran: Tunai</p>

    <div class="thankyou">
        <p>Terima kasih telah berbelanja!</p>
    </div>

</body>

</html>
