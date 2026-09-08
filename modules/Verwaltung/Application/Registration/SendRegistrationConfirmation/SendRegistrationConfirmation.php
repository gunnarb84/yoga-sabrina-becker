<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation;

use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Throwable;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\GenerateInvoicePdf;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class SendRegistrationConfirmation
{
    public function __construct(private GenerateInvoicePdf $pdfGenerator)
    {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $registration = Registration::findById($request->registrationId);

        if ($registration === null) {
            return Result::failure('registration.not_found');
        }

        $activity = Activity::findById($registration->aktivitaet_id);
        $participant = Participant::findById($registration->teilnehmer_id);

        if ($activity === null || $participant === null) {
            return Result::failure('registration.incomplete_data');
        }

        $pdfAttachment = null;

        if ($registration->zahlungsart === RegistrationPaymentMethod::Transfer) {
            $registrationIdBytes = Uuid::fromString($registration->id)->getBytes();
            $payment = Payment::whereRaw('anmeldung_id = ?', [$registrationIdBytes])
                ->where('methode', PaymentMethod::Transfer->value)
                ->where('beleg_art', 'rechnung')
                ->first();

            if ($payment !== null && $payment->beleg_id !== null) {
                $pdfResult = $this->pdfGenerator->execute(new GeneratePdfRequest($payment->beleg_id));

                if ($pdfResult->isSuccess()) {
                    $pdf = $pdfResult->unwrap();

                    $pdfAttachment = [
                        'filename' => $pdf->filename,
                        'content' => $pdf->content,
                        'mime' => 'application/pdf',
                    ];
                }
            }
        }

        $statusLabel = $registration->status === RegistrationStatus::WaitingList
            ? 'Warteliste'
            : 'Bestätigt';

        $paymentLabel = match ($registration->zahlungsart) {
            RegistrationPaymentMethod::Cash => 'Bar',
            RegistrationPaymentMethod::Transfer => 'Überweisung',
            RegistrationPaymentMethod::Free => 'Kostenlos',
        };

        $html = view('emails.registration-confirmation', [
            'activity' => $activity,
            'participant' => $participant,
            'registration' => $registration,
            'statusLabel' => $statusLabel,
            'paymentLabel' => $paymentLabel,
        ])->render();

        $subject = 'Anmeldebestätigung: '.$activity->titel;

        $message = new OutboundMessage([
            'anmeldung_id' => $registration->id,
            'empfaenger' => $participant->email,
            'betreff' => $subject,
            'inhalt' => $html,
            'status' => OutboundMessageStatus::Pending->value,
        ]);
        $message->save();

        try {
            $mail = new HtmlAttachmentMail($subject, $html, $pdfAttachment);

            Mail::to($participant->email)->send($mail);
            $message->markSent();
            $message->save();
        } catch (Throwable $e) {
            $message->markFailed($e->getMessage());
            $message->save();
        }

        return Result::success(new Response($message->id));
    }
}
