<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Throwable;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Platform\Shared\Application\Result;

final readonly class GenerateCashReceiptPdf
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $receipt = CashReceipt::findById($request->receiptId);

        if ($receipt === null) {
            return Result::failure('receipt.not_found');
        }

        $payment = Payment::findById($receipt->zahlung_id);

        if ($payment === null) {
            return Result::failure('payment.not_found');
        }

        $registration = Registration::findById($payment->anmeldung_id);

        if ($registration === null) {
            return Result::failure('registration.not_found');
        }

        $activity = Activity::findById($registration->aktivitaet_id);
        $participant = Participant::findById($registration->teilnehmer_id);

        if ($activity === null || $participant === null) {
            return Result::failure('receipt.incomplete_data');
        }

        try {
            $html = view('bareinnahmenbelege.pdf', [
                'receipt' => $receipt,
                'activity' => $activity,
                'participant' => $participant,
            ])->render();

            $options = new Options([
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'defaultFont' => 'Helvetica',
            ]);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4');
            $dompdf->render();

            return Result::success(new Response(
                $dompdf->output(),
                'Barquittung-'.$receipt->nummer.'.pdf',
            ));
        } catch (Throwable $e) {
            return Result::failure('receipt.pdf_generation_failed', [$e->getMessage()]);
        }
    }
}
