<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CashReceipt;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptListPdf\GenerateCashReceiptListPdf as GenerateListPdfOperation;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptListPdf\Request as GenerateListPdfRequest;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\Request as GeneratePdfRequest;

final readonly class CashReceiptController
{
    public function __construct(
        private GenerateCashReceiptPdf $pdfGenerator,
        private GenerateListPdfOperation $listPdfGenerator,
    ) {
    }

    public function download(string $id): Response
    {
        $result = $this->pdfGenerator->execute(new GeneratePdfRequest($id));

        if ($result->isFailure()) {
            // Der Download antwortet pauschal mit 404; die Ursache wird im Log
            // hinterlegt, damit Fehlkonfigurationen (z. B. der PDF-Erzeugung)
            // im Betrieb erkennbar bleiben.
            Log::warning('Barquittung-PDF wurde nicht erzeugt', $result->error() ?? []);

            abort(404);
        }

        $pdf = $result->unwrap();

        return response($pdf->content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdf->filename.'"',
        ]);
    }

    public function downloadMonth(string $monat): Response
    {
        $result = $this->listPdfGenerator->execute(new GenerateListPdfRequest($monat));

        if ($result->isFailure()) {
            Log::warning('Bareinnahmenliste-Monats-PDF wurde nicht erzeugt', $result->error() ?? []);

            abort(404);
        }

        $pdf = $result->unwrap();

        return response($pdf->content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdf->filename.'"',
        ]);
    }
}
