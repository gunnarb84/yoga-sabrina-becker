<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch;

use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment as RecordPaymentOperation;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Platform\Shared\Application\Result;

final readonly class RecordCashPaymentBatch
{
    public function __construct(
        private RecordPaymentOperation $recordPayment,
    ) {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $activity = Activity::findById($request->activityId);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        $outcomes = [];

        foreach ($request->items as $item) {
            $outcomes[] = $this->recordOne($item);
        }

        $recordedCount = count(array_filter($outcomes, fn (Outcome $outcome): bool => $outcome->outcome === 'erfasst'));

        return Result::success(new Response(
            $outcomes,
            $recordedCount,
            count($outcomes) - $recordedCount,
        ));
    }

    private function recordOne(Item $item): Outcome
    {
        $registration = Registration::findById($item->registrationId);

        if ($registration === null) {
            return new Outcome($item->registrationId, 'uebersprungen', 'registration.not_found');
        }

        if ($registration->zahlungsart !== RegistrationPaymentMethod::Cash) {
            return new Outcome($item->registrationId, 'uebersprungen', 'payment.method_not_supported');
        }

        $participant = Participant::findById($registration->teilnehmer_id);
        $activity = Activity::findById($registration->aktivitaet_id);

        if ($participant === null || $activity === null) {
            return new Outcome($item->registrationId, 'uebersprungen', 'registration.incomplete_data');
        }

        // Erfasst wird über den einzelnen Vorgang — Beleg, Nummernkreis und
        // E-Mail-Versand bleiben dort an einem Ort.
        $result = $this->recordPayment->execute(new RecordPaymentRequest(
            registrationId: $item->registrationId,
            method: RegistrationPaymentMethod::Cash->value,
            amount: (string) $activity->preis,
            paidAt: $item->paidAt,
            recipient: trim($participant->vorname.' '.$participant->nachname),
        ));

        if ($result->isFailure()) {
            $error = $result->error();

            return new Outcome(
                $item->registrationId,
                'uebersprungen',
                $error !== null ? $error['code'] : 'payment.failed',
            );
        }

        return new Outcome(
            $item->registrationId,
            'erfasst',
            receiptNumber: $result->unwrap()->documentNumber,
        );
    }
}
