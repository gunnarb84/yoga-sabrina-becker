<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordPaymentPrefill;

use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;

final readonly class RecordPaymentPrefillQuery
{
    /**
     * Liefert die Vorbelegung der Zahlungsmaske. `fall` entscheidet fachlich, ob
     * die Maske bedienbar ist (`editable`) oder eine bestehende Situation den
     * Vorgang verhindert (`cancelled`, `already_paid`, `transfer`); `null` heißt
     * `not_found`.
     *
     * @return object{fall: string, aktivitaet_id: string, betrag: string, empfaenger: string}|null
     */
    public function execute(string $registrationId): ?object
    {
        $registration = Registration::findById($registrationId);

        if ($registration === null) {
            return null;
        }

        $fall = $this->fall($registration);

        $activity = Activity::findById($registration->aktivitaet_id);
        $participant = Participant::findById($registration->teilnehmer_id);

        return (object) [
            'fall' => $fall,
            'aktivitaet_id' => $registration->aktivitaet_id,
            'betrag' => $activity !== null ? (string) $activity->preis : '',
            'empfaenger' => $participant === null ? '' : trim($participant->vorname.' '.$participant->nachname),
        ];
    }

    private function fall(Registration $registration): string
    {
        if ($registration->status === RegistrationStatus::Cancelled) {
            return 'cancelled';
        }

        if ($registration->zahlungsstatus === RegistrationPaymentStatus::Paid) {
            return 'already_paid';
        }

        if ($registration->zahlungsart === RegistrationPaymentMethod::Transfer) {
            return 'transfer';
        }

        return 'editable';
    }
}
