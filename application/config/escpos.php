<?php

return [
    'default_connection' => env('ESCPOS_CONNECTION', 'file'),
    'paper_width' => (int) env('ESCPOS_PAPER_WIDTH', 48),
    'file' => [
        'path' => env('ESCPOS_FILE_PATH', '/dev/usb/lp0'),
    ],
    'network' => [
        'host' => env('ESCPOS_NETWORK_HOST', ''),
        'port' => (int) env('ESCPOS_NETWORK_PORT', 9100),
    ],
    'windows' => [
        'name' => env('ESCPOS_WINDOWS_PRINTER', ''),
    ],
];
