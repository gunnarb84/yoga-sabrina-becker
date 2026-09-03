<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Throwable;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Platform\Shared\Application\Result;

final readonly class GenerateInvoicePdf
{
    public function execute(Request $request): Result
    {
        $invoice = Invoice::findById($request->invoiceId);

        if ($invoice === null) {
            return Result::failure('invoice.not_found');
        }

        $payment = Payment::findById($invoice->zahlung_id);

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
            return Result::failure('invoice.incomplete_data');
        }

        try {
            $html = view('invoices.pdf', [
                'invoice' => $invoice,
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
                'Rechnung-'.$invoice->nummer.'.pdf',
            ));
        } catch (Throwable $e) {
            return Result::failure('invoice.pdf_generation_failed', [$e->getMessage()]);
        }
    }
}
