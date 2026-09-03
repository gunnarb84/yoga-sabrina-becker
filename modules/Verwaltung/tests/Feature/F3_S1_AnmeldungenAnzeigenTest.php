<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F3 Anmeldungen und Warteliste verwalten / S1 Anmeldungen anzeigen
 *
 * Geprüfte Kriterien:
 * - AllRegistrationsQuery zeigt Veranstaltung, Teilnehmer/in, E-Mail, Status, Zahlungsart und Anmeldedatum.
 * - Die Liste lässt sich nach Veranstaltung, Status und Teilnehmer/in filtern.
 * - Bestätigte Anmeldungen und Wartelisten-Einträge werden unterschieden.
 * - Die Anzahl der freien Plätze und der Wartelisten-Einträge ist pro Veranstaltung sichtbar.
 */

use Yoga\Modules\Verwaltung\Application\Registration\AllRegistrations\AllRegistrationsQuery;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
    $this->first = TestFactory::createParticipant(email: 'erste@example.com', vorname: 'Anna', nachname: 'Alpha');
    $this->second = TestFactory::createParticipant(email: 'zweite@example.com', vorname: 'Berta', nachname: 'Beta');
    $this->third = TestFactory::createParticipant(email: 'dritte@example.com', vorname: 'Clara', nachname: 'Gamma');

    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(activityId: $this->activity->id, participantId: $this->first->id, paymentMethod: 'bar'));
    $register->execute(new RegisterRequest(activityId: $this->activity->id, participantId: $this->second->id, paymentMethod: 'ueberweisung'));
    $register->execute(new RegisterRequest(activityId: $this->activity->id, participantId: $this->third->id, paymentMethod: 'bar'));
});

it('returns all registrations with required fields', function (): void {
    $query = new AllRegistrationsQuery();
    $registrations = $query->execute();

    expect($registrations)->toHaveCount(3);

    $clara = null;
    foreach ($registrations as $registration) {
        if ($registration->teilnehmer_name === 'Clara Gamma') {
            $clara = $registration;
        }
    }

    expect($clara)->not->toBeNull();
    expect($clara->aktivitaet_titel)->toBe('Test-Workshop');
    expect($clara->email)->toBe('dritte@example.com');
    expect($clara->status)->toBeIn(['bestaetigt', 'warteliste']);
    expect($clara->zahlungsart)->toBe('bar');
    expect($clara->freie_plaetze)->toBeGreaterThanOrEqual(0);
    expect($clara->warteliste_anzahl)->toBeGreaterThanOrEqual(0);
});

it('differentiates confirmed and waiting-list entries', function (): void {
    $query = new AllRegistrationsQuery();
    $registrations = $query->execute();

    $statuses = array_map(fn (object $r): string => $r->status, $registrations);
    expect($statuses)->toContain('bestaetigt');
    expect($statuses)->toContain('warteliste');
});

it('filters by activity', function (): void {
    $otherActivity = TestFactory::createActivity();
    $otherParticipant = TestFactory::createParticipant(email: 'andere@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(activityId: $otherActivity->id, participantId: $otherParticipant->id, paymentMethod: 'bar'));

    $query = new AllRegistrationsQuery();
    $registrations = $query->execute(activityId: $this->activity->id);

    expect($registrations)->toHaveCount(3);
    foreach ($registrations as $registration) {
        expect($registration->aktivitaet_id)->toBe($this->activity->id);
    }
});

it('filters by status', function (): void {
    $query = new AllRegistrationsQuery();
    $registrations = $query->execute(status: 'warteliste');

    expect($registrations)->toHaveCount(1);
    expect($registrations[0]->status)->toBe('warteliste');
});

it('filters by participant search term', function (): void {
    $query = new AllRegistrationsQuery();
    $registrations = $query->execute(search: 'Alpha');

    expect($registrations)->toHaveCount(1);
    expect($registrations[0]->teilnehmer_name)->toBe('Anna Alpha');
});

it('filters by email search term', function (): void {
    $query = new AllRegistrationsQuery();
    $registrations = $query->execute(search: 'zweite@example.com');

    expect($registrations)->toHaveCount(1);
    expect($registrations[0]->teilnehmer_name)->toBe('Berta Beta');
});

it('shows free seats and waiting-list count per activity', function (): void {
    $query = new AllRegistrationsQuery();
    $registrations = $query->execute();

    foreach ($registrations as $registration) {
        expect($registration->freie_plaetze)->toBe(0);
        expect($registration->warteliste_anzahl)->toBe(1);
    }
});
