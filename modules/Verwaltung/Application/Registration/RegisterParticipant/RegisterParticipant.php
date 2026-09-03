<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Platform\NumberSequence\Application\NextNumber;
use Yoga\Platform\Shared\Application\DbValue;
use Yoga\Platform\Shared\Application\Result;

final readonly class RegisterParticipant
{
    public function __construct(private NextNumber $numbers)
    {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $activity = Activity::findById($request->activityId);

        $method = RegistrationPaymentMethod::tryFrom($request->paymentMethod);

        if ($method === null) {
            return Result::failure('registration.invalid_payment_method');
        }

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        $participant = Participant::findById($request->participantId);

        if ($participant === null) {
            return Result::failure('participant.not_found');
        }

        $activityIdBytes = Uuid::fromString($request->activityId)->getBytes();
        $participantIdBytes = Uuid::fromString($request->participantId)->getBytes();

        $existing = Registration::whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->whereRaw('teilnehmer_id = ?', [$participantIdBytes])
            ->where('status', '!=', RegistrationStatus::Cancelled->value)
            ->first();

        if ($existing !== null) {
            return Result::failure('registration.already_registered');
        }

        return DB::transaction(function () use ($activity, $participant, $method, $activityIdBytes): Result {
            $confirmedCount = Registration::whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                ->where('status', RegistrationStatus::Confirmed->value)
                ->count();

            $isFull = $confirmedCount >= $activity->maximale_teilnehmerzahl;
            $status = $isFull ? RegistrationStatus::WaitingList : RegistrationStatus::Confirmed;

            $registration = new Registration([
                'aktivitaet_id' => $activity->id,
                'teilnehmer_id' => $participant->id,
                'angemeldet_am' => now(),
                'status' => $status->value,
                'zahlungsart' => $method->value,
                'zahlungsstatus' => $this->initialPaymentStatus($method)->value,
            ]);

            $registration->save();

            $invoiceId = null;

            if ($method === RegistrationPaymentMethod::Transfer) {
                $invoiceId = $this->issueInvoice($registration, $activity, $participant);
            }

            $onWaitingList = false;

            if ($isFull) {
                $currentMaxRank = WaitingList::whereHas('anmeldung', function ($query) use ($activityIdBytes): void {
                    $query->whereRaw('aktivitaet_id = ?', [$activityIdBytes]);
                })->max('rang');

                $waitingList = new WaitingList([
                    'anmeldung_id' => $registration->id,
                    'rang' => DbValue::int($currentMaxRank ?? 0) + 1,
                ]);

                $waitingList->save();
                $onWaitingList = true;
            }

            return Result::success(new Response($registration->id, $onWaitingList, $invoiceId));
        });
    }

    private function initialPaymentStatus(RegistrationPaymentMethod $method): RegistrationPaymentStatus
    {
        return $method === RegistrationPaymentMethod::Free
            ? RegistrationPaymentStatus::Paid
            : RegistrationPaymentStatus::Open;
    }

    private function issueInvoice(Registration $registration, Activity $activity, Participant $participant): string
    {
        $payment = new Payment([
            'anmeldung_id' => $registration->id,
            'methode' => PaymentMethod::Transfer->value,
            'betrag' => $activity->preis,
            'bezahlt_am' => null,
        ]);

        $payment->save();

        $number = $this->numbers->next('R');

        $invoice = new Invoice([
            'nummer' => $number,
            'zahlung_id' => $payment->id,
            'ausgestellt_am' => now(),
            'empfaenger' => trim($participant->vorname.' '.$participant->nachname),
            'betrag' => $activity->preis,
        ]);

        $invoice->save();

        $payment->setAttribute('beleg_id', $invoice->id);
        $payment->setAttribute('beleg_art', 'rechnung');
        $payment->save();

        return $invoice->id;
    }
}
