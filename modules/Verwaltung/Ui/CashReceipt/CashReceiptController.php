<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CashReceipt;

use Illuminate\Http\Response;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\Request as GeneratePdfRequest;

final readonly class CashReceiptController
{
    public function __construct(
        private GenerateCashReceiptPdf $pdfGenerator,
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
}
