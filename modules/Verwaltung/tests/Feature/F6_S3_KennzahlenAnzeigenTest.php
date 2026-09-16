<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard / S3 Kennzahlen anzeigen
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt die Anzahl der Anmeldungen, die im laufenden Monat
 *   entstanden sind und nicht storniert sind.
 * - Das Dashboard zeigt die Summe der Beträge der Bareinnahmenbelege, die im
 *   laufenden Monat ausgestellt wurden, mit Währungsangabe.
 * - Das Dashboard zeigt den Kassenbestand über alle Zeiten.
 * - Die Abfrage `ListDashboardMetrics` liefert die drei Kennzahlen.
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\CashReceipt\AmountInWords;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\RecordCashWithdrawal;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\Request as WithdrawalRequest;
use Yoga\Modules\Verwaltung\Application\Dashboard\ListDashboardMetrics\ListDashboardMetricsQuery;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\CancelRegistration;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\Request as CancelRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Dashboard;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-02 12:00:00');
    Mail::fake();
    $this->query = new ListDashboardMetricsQuery();
    $this->activity = TestFactory::createActivity(maxParticipants: 5);
    $this->register = new RegisterParticipant(app(NextNumber::class));
    $this->recordPayment = new RecordPayment(
        app(NextNumber::class),
        new GenerateCashReceiptPdf(new AmountInWords()),
        new SendOutboundMessage(),
    );
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it("counts this month's non-cancelled registrations", function (): void {
    $first = $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: TestFactory::createParticipant(email: 'anna@example.com')->id,
        paymentMethod: 'bar',
    ));
    $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: TestFactory::createParticipant(email: 'berta@example.com')->id,
        paymentMethod: 'bar',
    ));

    app(CancelRegistration::class)->execute(new CancelRequest(
        registrationId: $first->unwrap()->registrationId,
    ));

    $metrics = $this->query->execute();

    expect($metrics->registrationsThisMonth)->toBe(1);
});

it('sums the cash receipts of the month and computes the cash balance', function (): void {
    $participant = TestFactory::createParticipant(email: 'anna@example.com');
    $registration = $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $this->recordPayment->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    (new RecordCashWithdrawal())->execute(new WithdrawalRequest(
        date: '2026-09-03',
        amount: '10.00',
        purpose: 'Barkauf',
    ));

    $metrics = $this->query->execute();

    expect($metrics->cashReceiptsMonth)->toBe('45');
    expect($metrics->cashReceiptsMonthCurrency)->toBe('EUR');
    expect($metrics->cashBalance)->toBe('35');
});

it('does not count cash receipts of previous months', function (): void {
    $participant = TestFactory::createParticipant(email: 'anna@example.com');
    $registration = $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $this->recordPayment->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-08-31 12:00:00',
        recipient: 'Max Mustermann',
        issuedAt: '2026-08-31 12:00:00',
    ));

    $metrics = $this->query->execute();

    expect($metrics->cashReceiptsMonth)->toBe('0');
    expect($metrics->cashBalance)->toBe('45');
});

it('shows the metrics on the dashboard', function (): void {
    $participant = TestFactory::createParticipant(email: 'anna@example.com');
    $registration = $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $this->recordPayment->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: PaymentMethod::Cash->value,
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    Livewire::test(Dashboard::class)
        ->assertSee('Kennzahlen')
        ->assertSee('Anmeldungen im laufenden Monat')
        ->assertSee('Bareinnahmen im laufenden Monat')
        ->assertSee('45,00 EUR')
        ->assertSee('Kassenbestand');
});
