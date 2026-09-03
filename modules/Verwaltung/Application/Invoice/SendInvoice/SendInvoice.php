<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\SendInvoice;

use Illuminate\Support\Facades\Mail;
use Throwable;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\GenerateInvoicePdf;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Ui\Mail\HtmlAttachmentMail;
use Yoga\Platform\Shared\Application\Result;

final readonly class SendInvoice
{
    public function __construct(private GenerateInvoicePdf $pdfGenerator)
    {
    }

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

        $pdfResult = $this->pdfGenerator->execute(new GeneratePdfRequest($request->invoiceId));

        if ($pdfResult->isFailure()) {
            return $pdfResult;
        }

        $pdf = $pdfResult->value();
        assert($pdf instanceof \Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\Response);

        $message = new OutboundMessage([
            'anmeldung_id' => $registration->getAttribute('id'),
            'empfaenger' => $participant->email,
            'betreff' => $request->subject,
            'inhalt' => $request->body,
            'status' => OutboundMessageStatus::Pending->value,
        ]);
        $message->save();

        try {
            $mail = new HtmlAttachmentMail(
                $request->subject,
                $request->body,
                [
                    'filename' => $pdf->filename,
                    'content' => $pdf->content,
                    'mime' => 'application/pdf',
                ],
            );

            Mail::to($participant->email)->send($mail);
            $message->markSent();
            $message->save();
        } catch (Throwable $e) {
            $message->markFailed($e->getMessage());
            $message->save();
        }

        return Result::success(new Response($message->getAttribute('id')));
    }
}
