<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Classes;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class PDFService
{
    public static function generate(InvoiceService $invoice, $template = 'default')
    {
        $template = strtolower($template);

        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);

        $pdf = new Dompdf($options);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]);

        $pdf->setHttpContext($context);

        $GLOBALS['with_pagination'] = $invoice->with_pagination;

        $pdf->loadHtml(View::make('invoices::' . $template, ['invoice' => $invoice]));
        $pdf->render();

        return $pdf;
    }
}
