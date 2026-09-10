<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordPayment;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\Request as SendOutboundMessageRequest;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage as MailSender;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Platform\NumberSequence\Application\NextNumber;
use Yoga\Platform\Shared\Application\Result;

final readonly class RecordPayment
{
    public function __construct(
        private NextNumber $numbers,
        private GenerateCashReceiptPdf $pdfGenerator,
        private MailSender $sender,
    ) {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        if ($request->method !== PaymentMethod::Cash->value) {
            return Result::failure('payment.method_not_supported');
        }

        $explicitNumber = null;

        if ($request->receiptNumber !== null) {
            $parsed = $this->parseReceiptNumber($request->receiptNumber);
            $explicitNumber = $parsed !== null
                ? ['text' => $request->receiptNumber, 'jahr' => $parsed['jahr'], 'nummer' => $parsed['nummer']]
                : null;

            if ($explicitNumber === null) {
                return Result::failure('receipt.number_invalid');
            }

            if (CashReceipt::where('nummer', $request->receiptNumber)->exists()) {
                return Result::failure('receipt.number_taken');
            }
        }

        $registration = Registration::findById($request->registrationId);

        if ($registration === null) {
            return Result::failure('registration.not_found');
        }

        $existingPayment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($request->registrationId)->getBytes()])->exists();

        if ($existingPayment) {
            return Result::failure('registration.already_paid');
        }

        $participant = Participant::findById($registration->teilnehmer_id);
        $activity = Activity::findById($registration->aktivitaet_id);

        if ($participant === null || $activity === null) {
            return Result::failure('registration.incomplete_data');
        }

        $result = DB::transaction(function () use ($registration, $request, $explicitNumber): Result {
            $payment = new Payment([
                'anmeldung_id' => $registration->id,
                'methode' => $request->method,
                'betrag' => $request->amount,
                'bezahlt_am' => $request->paidAt ?? now(),
            ]);

            $payment->save();

            [$documentId, $documentNumber, $documentType] = $this->issueCashReceipt($payment, $request, $explicitNumber);

            $payment->beleg_id = $documentId;
            $payment->beleg_art = $documentType;
            $payment->save();

            $registration->markAsPaid();
            $registration->save();

            return Result::success(new Response($payment->id, $documentNumber, $documentId));
        });

        if ($result->isSuccess() && $participant->email !== null && $participant->email !== '') {
            $this->sendReceiptEmail($result->unwrap(), $activity, $registration, $participant, $participant->email);
        }

        return $result;
    }

    private function sendReceiptEmail(Response $response, Activity $activity, Registration $registration, Participant $participant, string $email): void
    {
        $receipt = CashReceipt::findById($response->documentId);

        if ($receipt === null) {
            return;
        }

        $pdfResult = $this->pdfGenerator->execute(new GeneratePdfRequest($response->documentId));
        $attachment = null;

        if ($pdfResult->isSuccess()) {
            $pdf = $pdfResult->unwrap();

            $attachment = [
                'filename' => $pdf->filename,
                'content' => (string) $pdf->content,
                'mime' => 'application/pdf',
            ];
        }

        $html = view('emails.cash-receipt', [
            'receipt' => $receipt,
            'activity' => $activity,
            'participant' => $participant,
        ])->render();

        $this->sender->execute(new SendOutboundMessageRequest(
            recipient: $email,
            subject: 'Barquittung '.$receipt->nummer,
            html: $html,
            anmeldungId: $registration->id,
            attachment: $attachment,
        ));
    }

    /**
     * @param  array{text: string, jahr: int, nummer: int}|null  $explicitNumber
     * @return array{0: string, 1: string, 2: string}
     */
    private function issueCashReceipt(Payment $payment, Request $request, ?array $explicitNumber): array
    {
        if ($explicitNumber !== null) {
            $number = $explicitNumber['text'];

            // Nachpflege: der Nummernkreis zählt hinter der übernommenen
            // Nummer weiter, damit künftige Belege kollisionsfrei anschließen.
            $this->numbers->advance('B', $explicitNumber['nummer'], $explicitNumber['jahr']);
        } else {
            $number = $this->numbers->next('B');
        }

        $receipt = new CashReceipt([
            'nummer' => $number,
            'zahlung_id' => $payment->id,
            'ausgestellt_am' => $request->issuedAt !== null
                ? Carbon::parse($request->issuedAt)
                : now(),
            'empfaenger' => $request->recipient,
            'betrag' => $payment->betrag,
        ]);

        $receipt->save();

        return [$receipt->id, $number, 'bareinnahmenbeleg'];
    }

    /**
     * Prüft das Format `YYYY-NNNNN` und liefert Jahr und Nummer.
     *
     * @return array{jahr: int, nummer: int}|null
     */
    private function parseReceiptNumber(string $number): ?array
    {
        if (preg_match('/^(\d{4})-(\d{5})$/', $number, $matches) !== 1) {
            return null;
        }

        return ['jahr' => (int) $matches[1], 'nummer' => (int) $matches[2]];
    }
}
