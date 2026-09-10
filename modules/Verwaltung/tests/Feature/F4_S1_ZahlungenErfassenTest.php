<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten / S1 Zahlungen erfassen
 *
 * Geprüfte Kriterien:
 * - Der Vorgang RecordCashPayment legt eine Payment mit der Methode CASH an.
 * - RecordCashPayment erzeugt automatisch einen CashReceipt mit der nächsten freien
 *   lückenlosen Nummer im Format B-YYYY-NNNNN.
 * - RecordCashPayment scheitert mit REGISTRATION_NOT_FOUND, wenn die Anmeldung nicht existiert.
 * - RecordCashPayment scheitert mit ALREADY_PAID, wenn bereits eine Zahlung erfasst wurde.
 * - Der Zahlungsstatus der Anmeldung wird auf „bezahlt" gesetzt.
 * - Bei Überweisungen wird die Rechnung bereits bei der Anmeldung erstellt.
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Mail::fake();
    Carbon::setTestNow('2026-09-02 12:00:00');
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('records a cash payment and issues a receipt with the next number', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());
    $result = $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->documentNumber)->toBe('B-2026-00001');

    $payment = Payment::findById($result->unwrap()->paymentId);
    expect($payment)->not->toBeNull();
    expect($payment->methode)->toBe(PaymentMethod::Cash);
    expect($payment->betrag)->toBe('45.0000');

    $receipt = CashReceipt::where('nummer', 'B-2026-00001')->first();
    expect($receipt)->not->toBeNull();
    expect($receipt->empfaenger)->toBe('Max Mustermann');

    $registrationRecord = Registration::findById($registration->unwrap()->registrationId);
    expect($registrationRecord)->not->toBeNull();
    expect($registrationRecord->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);
});

it('creates an invoice at registration time for transfer payments', function (): void {
    $participant = TestFactory::createParticipant(vorname: 'Max', nachname: 'Mustermann', email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'ueberweisung',
    ));

    expect($registration->isSuccess())->toBeTrue();

    $registrationRecord = Registration::findById($registration->unwrap()->registrationId);
    expect($registrationRecord)->not->toBeNull();
    expect($registrationRecord->zahlungsstatus)->toBe(RegistrationPaymentStatus::Open);

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->unwrap()->registrationId)->getBytes()])->first();
    expect($payment)->not->toBeNull();
    expect($payment->methode)->toBe(PaymentMethod::Transfer);
    expect($payment->bezahlt_am)->toBeNull();

    $invoice = Invoice::where('nummer', 'R-2026-00001')->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->empfaenger)->toBe('Max Mustermann');
    expect($invoice->betrag)->toBe($this->activity->preis);
    expect($invoice->zahlung_id)->toBe($payment->id);
});

it('increments document numbers for consecutive payments', function (): void {
    $firstParticipant = TestFactory::createParticipant(email: 'erste@example.com');
    $secondParticipant = TestFactory::createParticipant(email: 'zweite@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $firstRegistration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $firstParticipant->id,
        paymentMethod: 'bar',
    ));
    $secondRegistration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $secondParticipant->id,
        paymentMethod: 'bar',
    ));

    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());

    $first = $record->execute(new RecordPaymentRequest(
        registrationId: $firstRegistration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));
    $second = $record->execute(new RecordPaymentRequest(
        registrationId: $secondRegistration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    expect($first->unwrap()->documentNumber)->toBe('B-2026-00001');
    expect($second->unwrap()->documentNumber)->toBe('B-2026-00002');
});

it('fails with not_found for a non-existing registration', function (): void {
    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());
    $result = $record->execute(new RecordPaymentRequest(
        registrationId: '018e1234-5678-7abc-8def-0123456789ab',
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('registration.not_found');
});

it('fails with already_paid when a payment was already recorded', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());
    $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    $second = $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    expect($second->isFailure())->toBeTrue();
    expect($second->error()['code'])->toBe('registration.already_paid');
});

it('fails when trying to record a transfer payment directly', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'ueberweisung',
    ));

    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());
    $result = $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Transfer->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('payment.method_not_supported');
});
