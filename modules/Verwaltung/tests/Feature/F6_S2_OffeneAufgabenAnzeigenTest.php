<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard / S2 Offene Aufgaben anzeigen
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt die Anzahl der Rechnungen mit Status `Offen`.
 * - Das Dashboard zeigt die Anzahl der ausgehenden Nachrichten mit Status
 *   `Fehlgeschlagen`.
 * - Das Dashboard zeigt die Anzahl der Anmeldungen mit Status `Warteliste`.
 * - Ein Klick auf einen Zähler öffnet die zugehörige Liste.
 * - Der Kontaktanfragen-Eintrag zeigt keinen Zähler; sein Klick öffnet die
 *   Kontaktanfragen-Liste.
 * - Ein Zähler `0` ist nicht sichtbar; ein Zähler größer als `0` ist sichtbar.
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\Dashboard\ListDashboardMetrics\ListDashboardMetricsQuery;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Dashboard;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-02 12:00:00');
    Mail::fake();
    $this->query = new ListDashboardMetricsQuery();
    $this->activity = TestFactory::createActivity(maxParticipants: 1);
    $this->register = new RegisterParticipant(app(NextNumber::class));
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('counts open invoices, failed messages and waiting list entries', function (): void {
    $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: TestFactory::createParticipant(email: 'rechnung@example.com')->id,
        paymentMethod: 'ueberweisung',
    ));
    $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: TestFactory::createParticipant(email: 'warteliste@example.com')->id,
        paymentMethod: 'bar',
    ));

    new OutboundMessage([
        'empfaenger' => 'anna@example.com',
        'betreff' => 'Barquittung',
        'inhalt' => 'Inhalt',
        'status' => OutboundMessageStatus::Failed->value,
        'versendet_am' => null,
    ])->save();

    $metrics = $this->query->execute();

    expect($metrics->openInvoices)->toBe(1);
    expect($metrics->failedMessages)->toBe(1);
    expect($metrics->waitingListEntries)->toBe(1);
});

it('shows only nonzero task counters with their links', function (): void {
    $this->register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: TestFactory::createParticipant(email: 'rechnung@example.com')->id,
        paymentMethod: 'ueberweisung',
    ));

    Livewire::test(Dashboard::class)
        ->assertSee('Offene Rechnungen')
        ->assertSee(route('verwaltung.invoices'))
        ->assertDontSee('Fehlgeschlagene E-Mails')
        ->assertDontSee('Wartelisteneinträge');
});

it('shows the contact inquiries entry without a counter', function (): void {
    Livewire::test(Dashboard::class)
        ->assertSee('Kontaktanfragen')
        ->assertSee(route('verwaltung.contact-inquiries'));
});
