<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment;

use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment as RecordPaymentOperation;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationSource;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class RecordWalkInCashPayment
{
    public function __construct(
        private RecordPaymentOperation $recordPayment,
    ) {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $firstName = trim($request->firstName);
        $lastName = trim($request->lastName);

        if ($firstName === '' || $lastName === '') {
            return Result::failure('participant.name_required');
        }

        if ($request->email !== null && $request->email !== ''
            && filter_var($request->email, FILTER_VALIDATE_EMAIL) === false) {
            return Result::failure('participant.email_invalid');
        }

        if ((float) $request->amount <= 0.0) {
            return Result::failure('payment.amount_invalid');
        }

        // Vorab prüfen, damit bei einer ungültigen oder vergebenen Nummer
        // keine Teilnehmer-/Anmeldedaten zurückbleiben.
        if ($request->receiptNumber !== null) {
            if (! $this->parseReceiptNumber($request->receiptNumber)) {
                return Result::failure('receipt.number_invalid');
            }

            if (CashReceipt::where('nummer', $request->receiptNumber)->exists()) {
                return Result::failure('receipt.number_taken');
            }
        }

        $activity = Activity::findById($request->activityId);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        $activityIdBytes = Uuid::fromString($request->activityId)->getBytes();
        $participant = $this->findOrNone($firstName, $lastName);

        if ($participant === null) {
            $participant = $this->createParticipant($request, $firstName, $lastName);
        }

        $existing = Registration::whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->whereRaw('teilnehmer_id = ?', [Uuid::fromString($participant->id)->getBytes()])
            ->where('status', '!=', RegistrationStatus::Cancelled->value)
            ->first();

        if ($existing !== null) {
            return Result::failure('registration.already_registered');
        }

        // Die Person ist anwesend und hat bereits gezahlt: keine Kapazitäts-
        // oder Wartelistenprüfung, die Anmeldung entsteht direkt bestätigt.
        $registration = new Registration([
            'aktivitaet_id' => $activity->id,
            'teilnehmer_id' => $participant->id,
            'angemeldet_am' => now(),
            'status' => RegistrationStatus::Confirmed->value,
            'zahlungsart' => RegistrationPaymentMethod::Cash->value,
            'zahlungsstatus' => RegistrationPaymentStatus::Open->value,
            'herkunft' => RegistrationSource::Administration->value,
        ]);

        $registration->save();

        // Erfasst wird über den einzelnen Vorgang — Beleg, Nummernkreis,
        // Nachpflege und E-Mail-Versand bleiben dort an einem Ort.
        $result = $this->recordPayment->execute(new RecordPaymentRequest(
            registrationId: $registration->id,
            method: RegistrationPaymentMethod::Cash->value,
            amount: $request->amount,
            paidAt: $request->paidAt,
            recipient: $firstName.' '.$lastName,
            receiptNumber: $request->receiptNumber,
            issuedAt: $request->issuedAt,
        ));

        if ($result->isFailure()) {
            $error = $result->error();

            return Result::failure($error !== null ? (string) $error['code'] : 'payment.failed');
        }

        $payment = $result->unwrap();

        return Result::success(new Response(
            $payment->paymentId,
            (string) $payment->documentNumber,
            $payment->documentId,
        ));
    }

    /**
     * Prüft das Format `YYYY-NNNNN`.
     */
    private function parseReceiptNumber(string $number): bool
    {
        return preg_match('/^\d{4}-\d{5}$/', $number) === 1;
    }

    private function findOrNone(string $firstName, string $lastName): ?Participant
    {
        // Exakte Übereinstimmung von Vor- und Nachname (ohne Rücksicht auf
        // Groß-/Kleinschreibung) — vorhandene Stammdaten bleiben unverändert.
        return Participant::query()
            ->whereRaw('vorname = ? AND nachname = ?', [$firstName, $lastName])
            ->first();
    }

    private function createParticipant(Request $request, string $firstName, string $lastName): Participant
    {
        $email = $request->email !== null && $request->email !== '' ? $request->email : null;

        $participant = new Participant([
            'email' => $email,
            'vorname' => $firstName,
            'nachname' => $lastName,
        ]);

        $participant->save();

        return $participant;
    }
}
