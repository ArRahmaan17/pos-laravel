<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Debug Print</title>
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

        .label {
            font-weight: 700;
        }

        .center {
            text-align: center;
        }

        .barcode-line {
            letter-spacing: 2px;
            font-weight: 700;
            text-align: center;
            margin: 8px 0;
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
    <h1>{{ $companyName }}</h1>
    <p>THERMAL DEBUG PRINT</p>
    <p class="muted">{{ $generatedAt }}</p>

    <div class="divider"></div>

    <div class="row">
        <div class="label">Printer Width</div>
        <div>{{ $printerWidth }}</div>
    </div>
    <div class="row">
        <div class="label">Character Test</div>
        <div>1234567890</div>
    </div>
    <div class="row">
        <div class="label">Symbols</div>
        <div>.-_=+/\\*</div>
    </div>

    <div class="divider"></div>

    @foreach ($sampleLines as $line)
        <div class="row">
            <div>{{ $line['label'] }}</div>
            <div>{{ $line['value'] }}</div>
        </div>
    @endforeach

    <div class="divider"></div>

    <div class="center">LEFT</div>
    <div class="center">CENTER</div>
    <div class="center">RIGHT</div>

    <div class="divider"></div>

    <div class="barcode-line">|| ||| |||| ||| ||</div>
    <div class="center muted">If spacing looks wrong, adjust browser margins/scaling.</div>

    <div class="divider"></div>

    <div class="center">
        End of debug print
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>
