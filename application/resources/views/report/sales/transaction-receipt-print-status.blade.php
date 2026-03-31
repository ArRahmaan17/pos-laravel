<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Print Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 24px;
            background: #f7f7f9;
            color: #222;
        }

        .card {
            max-width: 640px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .status-ok {
            color: #0a7a35;
        }

        .status-error {
            color: #b42318;
        }

        code {
            background: #f2f4f7;
            padding: 2px 6px;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2 class="{{ $success ? 'status-ok' : 'status-error' }}">{{ $success ? 'Receipt sent to printer' : 'Receipt print failed' }}</h2>
        <p><strong>Order:</strong> {{ $orderCode }}</p>
        <p>{{ $message }}</p>

        @if (!empty($details))
            <p><strong>Details:</strong> <code>{{ $details }}</code></p>
        @endif
    </div>
</body>

</html>
