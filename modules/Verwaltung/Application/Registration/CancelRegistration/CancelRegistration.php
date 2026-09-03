<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\CashReturn\CashReturn;
use Yoga\Modules\Verwaltung\Domain\CreditNote\CreditNote;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Platform\NumberSequence\Application\NextNumber;
use Yoga\Platform\Shared\Application\Result;

final readonly class CancelRegistration
{
    public function __construct(private NextNumber $numbers)
    {
    }

    public function execute(Request $request): Result
    {
        $registration = Registration::findById($request->registrationId);

        if ($registration !== null) {
            $registration->load('wartelistenEintrag');
        }

        if ($registration === null) {
            return Result::failure('registration.not_found');
        }

        if ($registration->status === RegistrationStatus::Cancelled) {
            return Result::failure('registration.already_cancelled');
        }

        return DB::transaction(function () use ($registration): Result {
            $wasWaiting = $registration->status === RegistrationStatus::WaitingList;
            $activityId = Uuid::fromString($registration->getAttribute('aktivitaet_id'))->getBytes();

            if ($registration->zahlungsstatus === RegistrationPaymentStatus::Paid) {
                $this->issueReversalDocument($registration);
            }

            $registration->cancel();
            $registration->save();

            if ($registration->wartelistenEintrag !== null) {
                $registration->wartelistenEintrag->delete();
            }

            if (! $wasWaiting) {
                $this->promoteFirstWaiting($activityId);
            }

            return Result::success();
        });
    }

    private function issueReversalDocument(Registration $registration): void
    {
        $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->getAttribute('id'))->getBytes()])->first();

        if ($payment === null) {
            return;
        }

        match ($registration->zahlungsart) {
            RegistrationPaymentMethod::Transfer => $this->issueCreditNote($payment),
            RegistrationPaymentMethod::Cash => $this->issueCashReturn($payment),
            default => null,
        };
    }

    private function issueCreditNote(Payment $payment): void
    {
        $invoiceId = $payment->getAttribute('beleg_id');

        if ($invoiceId === null || $payment->getAttribute('beleg_art') !== 'rechnung') {
            return;
        }

        $invoice = Invoice::findById($invoiceId);

        if ($invoice === null) {
            return;
        }

        $creditNote = new CreditNote([
            'nummer' => $this->numbers->next('G'),
            'rechnung_id' => $invoice->getAttribute('id'),
            'ausgestellt_am' => now(),
            'empfaenger' => $invoice->empfaenger,
            'betrag' => $invoice->betrag,
        ]);

        $creditNote->save();
    }

    private function issueCashReturn(Payment $payment): void
    {
        $receiptId = $payment->getAttribute('beleg_id');

        if ($receiptId === null || $payment->getAttribute('beleg_art') !== 'bareinnahmenbeleg') {
            return;
        }

        $receipt = CashReceipt::findById($receiptId);

        if ($receipt === null) {
            return;
        }

        $cashReturn = new CashReturn([
            'nummer' => $this->numbers->next('RB'),
            'bareinnahmenbeleg_id' => $receipt->getAttribute('id'),
            'ausgestellt_am' => now(),
            'empfaenger' => $receipt->empfaenger,
            'betrag' => $receipt->betrag,
        ]);

        $cashReturn->save();
    }

    private function promoteFirstWaiting(string $activityId): void
    {
        $next = WaitingList::whereHas('anmeldung', function ($query) use ($activityId): void {
            $query->whereRaw('aktivitaet_id = ?', [$activityId])
                ->where('status', RegistrationStatus::WaitingList->value);
        })
            ->orderBy('rang')
            ->first();

        if ($next === null) {
            return;
        }

        $registration = Registration::findById($next->getAttribute('anmeldung_id'));

        if ($registration === null) {
            return;
        }

        $next->promote();
        $next->save();

        $registration->confirm();
        $registration->save();
    }
}
