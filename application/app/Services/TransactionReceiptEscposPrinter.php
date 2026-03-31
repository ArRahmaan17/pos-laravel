<?php

namespace App\Services;

use App\Models\CustomerProductTransaction;
use InvalidArgumentException;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class TransactionReceiptEscposPrinter
{
    public function print(CustomerProductTransaction $transaction): void
    {
        $connector = $this->makeConnector();
        $printer = new Printer($connector);

        try {
            $company = session('userLogged')['company'];
            $paperWidth = max(32, (int) config('escpos.paper_width', 48));
            $addressParts = array_filter([
                $company['address']['place'] ?? null,
                $company['address']['address'] ?? null,
                $company['address']['city'] ?? null,
                $company['address']['province'] ?? null,
                $company['address']['zip_code'] ?? null,
            ]);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text($company['name']."\n");
            $printer->setEmphasis(false);
            $printer->text(implode(', ', $addressParts)."\n");
            if (! empty($company['phone_number'])) {
                $printer->text($company['phone_number']."\n");
            }
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Receipt : '.$transaction->orderCode."\n");
            $printer->text('Date    : '.$transaction->created_at?->format('Y-m-d H:i:s')."\n");
            $printer->text(str_repeat('-', $paperWidth)."\n");

            foreach ($transaction->details as $detail) {
                $name = $detail->good->name ?? 'Unknown Item';
                $qty = (int) $detail->quantity;
                $unitPrice = (float) ($detail->price ?? $detail->good->price ?? 0);
                $lineTotal = $unitPrice * $qty;

                $printer->text($name."\n");
                $printer->text($this->twoColumn(
                    numberFormat($unitPrice).' x '.$qty,
                    numberFormat($lineTotal),
                    $paperWidth
                ));
            }

            $printer->text(str_repeat('-', $paperWidth)."\n");
            $printer->text($this->twoColumn('Subtotal', numberFormat((float) $transaction->total), $paperWidth));
            $printer->text($this->twoColumn('Discount', numberFormat((float) $transaction->discount), $paperWidth));
            $printer->setEmphasis(true);
            $printer->text($this->twoColumn(
                'Total',
                numberFormat((float) $transaction->total - (float) $transaction->discount),
                $paperWidth
            ));
            $printer->setEmphasis(false);
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for shopping\n");
            $printer->feed(3);
            $printer->cut();
        } finally {
            $printer->close();
        }
    }

    private function makeConnector(): FilePrintConnector|NetworkPrintConnector|WindowsPrintConnector
    {
        return match (config('escpos.default_connection', 'file')) {
            'network' => $this->networkConnector(),
            'windows' => $this->windowsConnector(),
            default => $this->fileConnector(),
        };
    }

    private function fileConnector(): FilePrintConnector
    {
        $path = (string) config('escpos.file.path');
        if ($path === '') {
            throw new InvalidArgumentException('ESCPOS_FILE_PATH is not configured.');
        }

        return new FilePrintConnector($path);
    }

    private function networkConnector(): NetworkPrintConnector
    {
        $host = (string) config('escpos.network.host');
        $port = (int) config('escpos.network.port', 9100);
        if ($host === '') {
            throw new InvalidArgumentException('ESCPOS_NETWORK_HOST is not configured.');
        }

        return new NetworkPrintConnector($host, $port);
    }

    private function windowsConnector(): WindowsPrintConnector
    {
        $name = (string) config('escpos.windows.name');
        if ($name === '') {
            throw new InvalidArgumentException('ESCPOS_WINDOWS_PRINTER is not configured.');
        }

        return new WindowsPrintConnector($name);
    }

    private function twoColumn(string $left, string $right, int $width): string
    {
        $maxRight = min(strlen($right), (int) floor($width / 2));
        $right = substr($right, 0, $maxRight);
        $leftWidth = max(1, $width - strlen($right) - 1);
        $left = substr($left, 0, $leftWidth);

        return str_pad($left, $leftWidth).' '.$right."\n";
    }
}
