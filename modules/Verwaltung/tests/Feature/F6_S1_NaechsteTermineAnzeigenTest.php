<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard / S1 Nächste Termine anzeigen
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt unter „Nächste Termine" alle Termine vom heutigen Datum
 *   bis zum Ende des laufenden Monats, aufsteigend nach Beginn.
 * - Termine mit Beginn vor dem heutigen Datum werden nicht angezeigt.
 * - Jede Terminzeile zeigt Titel, Beginn, bestätigte Anmeldungen und freie Plätze.
 * - Ein Klick auf eine Terminzeile öffnet die Anmeldungsliste der Veranstaltung.
 * - Sind keine Termine im Zeitraum vorhanden, zeigt der Block den Hinweis.
 * - Die Abfrage `ListUpcomingSessions` liefert die Termine des Zeitraums.
 */

use Carbon\Carbon;
use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Application\Session\ListUpcomingSessions\ListUpcomingSessionsQuery;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Dashboard;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-02 12:00:00');
    $this->query = new ListUpcomingSessionsQuery();
    $this->activity = TestFactory::createActivity(maxParticipants: 10);
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('lists sessions from today to the end of the month ascending', function (): void {
    $yesterday = TestFactory::createSession($this->activity, Carbon::parse('2026-09-01 18:00'));
    $today = TestFactory::createSession($this->activity, Carbon::parse('2026-09-02 18:00'));
    $later = TestFactory::createSession($this->activity, Carbon::parse('2026-09-20 10:00'));
    $nextMonth = TestFactory::createSession($this->activity, Carbon::parse('2026-10-05 10:00'));

    $sessions = $this->query->execute();

    expect($sessions)->toHaveCount(2);
    expect($sessions[0]->beginn)->toContain('2026-09-02');
    expect($sessions[1]->beginn)->toContain('2026-09-20');
});

it('delivers the activity title, capacity, confirmed count and free seats', function (): void {
    TestFactory::createSession($this->activity, Carbon::parse('2026-09-05 10:00'));

    $participant = TestFactory::createParticipant(email: 'anna@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: TestFactory::createParticipant(email: 'berta@example.com')->id,
        paymentMethod: 'bar',
    ));

    $sessions = $this->query->execute();

    expect($sessions[0]->titel)->toBe('Test-Workshop');
    expect($sessions[0]->maxParticipants)->toBe(10);
    expect($sessions[0]->confirmed)->toBe(2);
    expect($sessions[0]->freeSeats)->toBe(8);
});

it('shows the upcoming sessions on the dashboard with a link to the registrations', function (): void {
    TestFactory::createSession($this->activity, Carbon::parse('2026-09-05 10:00'));

    Livewire::test(Dashboard::class)
        ->assertSee('Nächste Termine')
        ->assertSee('Test-Workshop')
        ->assertSee('05.09.2026, 10:00')
        ->assertSee('freie Plätze')
        ->assertSee(route('verwaltung.activity.registrations', ['id' => $this->activity->id]));
});

it('shows the empty hint when no session is in the month', function (): void {
    Livewire::test(Dashboard::class)
        ->assertSee('Keine Termine im laufenden Monat.');
});
