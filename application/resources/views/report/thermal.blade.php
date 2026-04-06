<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report_title }} {{ $customer_company }}</title>
    <style>
        :root {
            color-scheme: light;
        }

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
        h2,
        p {
            margin: 0;
        }

        .center {
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

        .section {
            margin-top: 12px;
        }

        .section-title {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
        }

        .row-label {
            flex: 1 1 auto;
        }

        .row-value {
            flex: 0 0 auto;
            text-align: right;
        }

        .entry {
            padding: 6px 0;
            border-bottom: 1px dotted #bbb;
        }

        .entry:last-child {
            border-bottom: 0;
        }

        .entry-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-weight: 700;
        }

        .entry-value {
            text-align: right;
        }

        .entry-meta {
            margin-top: 2px;
            color: #555;
            font-size: 11px;
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
    <div class="center">
        <h1>{{ $customer_company }}</h1>
        <p>{{ $report_title }}</p>
        <p class="muted">{{ $generated_date }}</p>
    </div>

    <div class="divider"></div>

    <div class="section">
        <div class="row">
            <div class="row-label">Period</div>
            <div class="row-value">{{ $report_period }}</div>
        </div>
    </div>

    @foreach ($sections as $section)
        <div class="divider"></div>
        <div class="section">
            <div class="section-title">{{ $section['title'] }}</div>

            @foreach ($section['rows'] ?? [] as $row)
                <div class="row">
                    <div class="row-label">{{ $row['label'] }}</div>
                    <div class="row-value">{{ $row['value'] }}</div>
                </div>
            @endforeach

            @foreach ($section['entries'] ?? [] as $entry)
                <div class="entry">
                    <div class="entry-head">
                        <div>{{ $entry['title'] }}</div>
                        <div class="entry-value">{{ $entry['value'] ?? '' }}</div>
                    </div>
                    @if (!empty($entry['meta']))
                        <div class="entry-meta">{{ $entry['meta'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>
