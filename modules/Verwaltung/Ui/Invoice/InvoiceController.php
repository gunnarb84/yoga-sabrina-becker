<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Invoice;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\GenerateInvoicePdf;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\Invoice\SendInvoice\Request as SendInvoiceRequest;
use Yoga\Modules\Verwaltung\Application\Invoice\SendInvoice\SendInvoice;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;

final readonly class InvoiceController
{
    public function __construct(
        private GenerateInvoicePdf $pdfGenerator,
        private SendInvoice $sendInvoice,
    ) {
    }

    public function download(string $id): Response
    {
        $result = $this->pdfGenerator->execute(new GeneratePdfRequest($id));

        if ($result->isFailure()) {
            abort(404);
        }

        $pdf = $result->unwrap();

        return response($pdf->content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdf->filename.'"',
        ]);
    }

    public function sendEmail(string $id): RedirectResponse
    {
        $invoice = Invoice::findById($id);

        if ($invoice === null) {
            abort(404);
        }

        $subject = 'Rechnung '.$invoice->nummer;
        $body = view('emails.invoice-resend', [
            'invoice' => $invoice,
        ])->render();

        $this->sendInvoice->execute(new SendInvoiceRequest(
            invoiceId: $id,
            subject: $subject,
            body: $body,
        ));

        return Redirect::route('verwaltung.invoices')->with('status', 'Rechnung wurde erneut versendet.');
    }
}
