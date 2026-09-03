<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten / S3 Stornierungen mit Belegen erfassen
 *
 * Geprüfte Kriterien:
 * - Bei Stornierung einer Anmeldung mit Zahlungsart „Überweisung" wird eine CreditNote mit
 *   der nächsten freien Nummer im Format G-YYYY-NNNNN erzeugt.
 * - Bei Stornierung einer Anmeldung mit Zahlungsart „Bar" wird eine CashReturn mit der
 *   nächsten freien Nummer im Format RB-YYYY-NNNNN erzeugt.
 * - Beide Belege verweisen auf die ursprüngliche Rechnung bzw. den ursprünglichen
 *   Bareinnahmenbeleg.
 */

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid\MarkTransferPaid;
use Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid\Request as MarkTransferPaidRequest;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\CancelRegistration;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\Request as CancelRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\CashReturn\CashReturn;
use Yoga\Modules\Verwaltung\Domain\CreditNote\CreditNote;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-02 12:00:00');
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('creates a credit note when cancelling a paid transfer registration', function (): void {
    $participant = TestFactory::createParticipant(vorname: 'Max', nachname: 'Mustermann', email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'ueberweisung',
    ));

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->unwrap()->registrationId)->getBytes()])->first();
    $invoice = Invoice::whereRaw('zahlung_id = ?', [Uuid::fromString($payment->id)->getBytes()])->first();

    $mark = new MarkTransferPaid();
    $mark->execute(new MarkTransferPaidRequest(invoiceId: $invoice->id));

    $cancel = app(CancelRegistration::class);
    $result = $cancel->execute(new CancelRequest(
        registrationId: $registration->unwrap()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $creditNote = CreditNote::where('nummer', 'G-2026-00001')->first();
    expect($creditNote)->not->toBeNull();
    expect($creditNote->empfaenger)->toBe('Max Mustermann');
    expect($creditNote->rechnung_id)->toBe($invoice->id);

    $registrationRecord = Registration::findById($registration->unwrap()->registrationId);
    expect($registrationRecord)->not->toBeNull();
    expect($registrationRecord->status)->toBe(RegistrationStatus::Cancelled);
});

it('creates a cash return when cancelling a paid cash registration', function (): void {
    $participant = TestFactory::createParticipant(vorname: 'Max', nachname: 'Mustermann', email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $record = new RecordPayment(app(NextNumber::class));
    $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Cash,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->unwrap()->registrationId)->getBytes()])->first();
    $receipt = CashReceipt::whereRaw('zahlung_id = ?', [Uuid::fromString($payment->id)->getBytes()])->first();

    $cancel = app(CancelRegistration::class);
    $result = $cancel->execute(new CancelRequest(
        registrationId: $registration->unwrap()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $cashReturn = CashReturn::where('nummer', 'RB-2026-00001')->first();
    expect($cashReturn)->not->toBeNull();
    expect($cashReturn->empfaenger)->toBe('Max Mustermann');
    expect($cashReturn->bareinnahmenbeleg_id)->toBe($receipt->id);
});

it('does not create a reversal document when cancelling an unpaid transfer registration', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'ueberweisung',
    ));

    $cancel = app(CancelRegistration::class);
    $result = $cancel->execute(new CancelRequest(
        registrationId: $registration->unwrap()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $creditNote = CreditNote::where('nummer', 'G-2026-00001')->first();
    expect($creditNote)->toBeNull();
});

it('increments reversal document numbers for consecutive cancellations', function (): void {
    $firstParticipant = TestFactory::createParticipant(vorname: 'Max', nachname: 'Mustermann', email: 'erste@example.com');
    $secondParticipant = TestFactory::createParticipant(vorname: 'Erika', nachname: 'Musterfrau', email: 'zweite@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $firstRegistration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $firstParticipant->id,
        paymentMethod: 'ueberweisung',
    ));
    $secondRegistration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $secondParticipant->id,
        paymentMethod: 'ueberweisung',
    ));

    $firstPayment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($firstRegistration->unwrap()->registrationId)->getBytes()])->first();
    $firstInvoice = Invoice::whereRaw('zahlung_id = ?', [Uuid::fromString($firstPayment->id)->getBytes()])->first();
    $secondPayment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($secondRegistration->unwrap()->registrationId)->getBytes()])->first();
    $secondInvoice = Invoice::whereRaw('zahlung_id = ?', [Uuid::fromString($secondPayment->id)->getBytes()])->first();

    $mark = new MarkTransferPaid();
    $mark->execute(new MarkTransferPaidRequest(invoiceId: $firstInvoice->id));
    $mark->execute(new MarkTransferPaidRequest(invoiceId: $secondInvoice->id));

    $cancel = app(CancelRegistration::class);
    $cancel->execute(new CancelRequest(registrationId: $firstRegistration->unwrap()->registrationId));
    $cancel->execute(new CancelRequest(registrationId: $secondRegistration->unwrap()->registrationId));

    $firstCreditNote = CreditNote::where('nummer', 'G-2026-00001')->first();
    $secondCreditNote = CreditNote::where('nummer', 'G-2026-00002')->first();

    expect($firstCreditNote)->not->toBeNull();
    expect($secondCreditNote)->not->toBeNull();
});
