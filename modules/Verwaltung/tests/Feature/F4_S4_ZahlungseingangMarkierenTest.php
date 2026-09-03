<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten / S4 Zahlungseingang markieren
 *
 * Geprüfte Kriterien:
 * - Das paidAt-Feld der Zahlung wird auf das aktuelle Datum gesetzt.
 * - Der Status der Rechnung ändert sich auf „bezahlt".
 * - Die Markierung ist protokolliert (Person, Zeitpunkt).
 * - Eine einmal als bezahlt markierte Rechnung kann wieder auf „offen" gesetzt werden.
 */

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid\MarkTransferPaid;
use Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid\Request as MarkTransferPaidRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Invoice\InvoiceStatus;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-02 12:00:00');
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('marks a transfer invoice as paid and updates the registration payment status', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'ueberweisung',
    ));

    $registrationRecord = Registration::findById($registration->unwrap()->registrationId);
    expect($registrationRecord->zahlungsstatus)->toBe(RegistrationPaymentStatus::Open);

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->unwrap()->registrationId)->getBytes()])->first();
    expect($payment)->not->toBeNull();

    $invoice = Invoice::whereRaw('zahlung_id = ?', [Uuid::fromString($payment->id)->getBytes()])->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->status)->toBe(InvoiceStatus::Open);

    $mark = new MarkTransferPaid();
    $result = $mark->execute(new MarkTransferPaidRequest(
        invoiceId: $invoice->id,
        userId: '018e1234-5678-7abc-8def-0123456789ab',
    ));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->status)->toBe(InvoiceStatus::Paid);

    $invoice = $invoice->fresh();
    expect($invoice->status)->toBe(InvoiceStatus::Paid);
    expect($invoice->geaendert_von)->toBe('018e1234-5678-7abc-8def-0123456789ab');

    $payment = $payment->fresh();
    expect($payment->bezahlt_am)->not->toBeNull();

    $registrationRecord = $registrationRecord->fresh();
    expect($registrationRecord->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);
});

it('toggles a paid invoice back to open', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
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
    $result = $mark->execute(new MarkTransferPaidRequest(invoiceId: $invoice->id));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->status)->toBe(InvoiceStatus::Open);

    $invoice = $invoice->fresh();
    expect($invoice->status)->toBe(InvoiceStatus::Open);

    $payment = $payment->fresh();
    expect($payment->bezahlt_am)->toBeNull();

    $registration = Registration::findById($registration->unwrap()->registrationId);
    expect($registration->zahlungsstatus)->toBe(RegistrationPaymentStatus::Open);
});

it('fails with not_found for a non-existing invoice', function (): void {
    $mark = new MarkTransferPaid();
    $result = $mark->execute(new MarkTransferPaidRequest(
        invoiceId: '018e1234-5678-7abc-8def-0123456789ab',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('invoice.not_found');
});
