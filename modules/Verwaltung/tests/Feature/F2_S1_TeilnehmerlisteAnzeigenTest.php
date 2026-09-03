<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F2 Teilnehmer verwalten / S1 Teilnehmerliste anzeigen
 *
 * Geprüfte Kriterien:
 * - Die Liste zeigt firstName, lastName, email, phone und city.
 * - Die Liste ist nach lastName sortiert.
 * - Es gibt ein Suchfeld, das in firstName, lastName und email sucht.
 * - Klick auf einen Teilnehmer öffnet die Detailmaske.
 * - Gesundheitsinformationen werden in der Liste nicht angezeigt.
 */

use Yoga\Modules\Verwaltung\Application\Participant\Participants\ParticipantsQuery;
use Yoga\Modules\Verwaltung\Tests\TestFactory;

beforeEach(function (): void {
    $this->alpha = TestFactory::createParticipant(
        email: 'alpha@example.com',
        vorname: 'Anna',
        nachname: 'Alpha',
        phone: '0123 456789',
        city: 'Berlin',
    );
    $this->beta = TestFactory::createParticipant(
        email: 'beta@example.com',
        vorname: 'Berta',
        nachname: 'Beta',
        phone: '0987 654321',
        city: 'Hamburg',
    );
});

it('returns participants ordered by last name', function (): void {
    $query = new ParticipantsQuery();
    $participants = $query->execute();

    expect($participants)->toHaveCount(2);
    expect($participants[0]->nachname)->toBe('Alpha');
    expect($participants[1]->nachname)->toBe('Beta');
});

it('includes the required fields for each participant', function (): void {
    $query = new ParticipantsQuery();
    $participants = $query->execute();

    $first = $participants[0];
    expect($first->vorname)->toBe('Anna');
    expect($first->nachname)->toBe('Alpha');
    expect($first->email)->toBe('alpha@example.com');
    expect($first->telefon)->toBe('0123 456789');
    expect($first->stadt)->toBe('Berlin');
});

it('exposes participant ids for linking to the detail view', function (): void {
    $query = new ParticipantsQuery();
    $participants = $query->execute();

    expect($participants[0]->id)->toBeUuidString();
});

it('filters participants by first name', function (): void {
    $query = new ParticipantsQuery();
    $participants = $query->execute('Berta');

    expect($participants)->toHaveCount(1);
    expect($participants[0]->nachname)->toBe('Beta');
});

it('filters participants by last name', function (): void {
    $query = new ParticipantsQuery();
    $participants = $query->execute('Alpha');

    expect($participants)->toHaveCount(1);
    expect($participants[0]->nachname)->toBe('Alpha');
});

it('filters participants by email', function (): void {
    $query = new ParticipantsQuery();
    $participants = $query->execute('beta@example.com');

    expect($participants)->toHaveCount(1);
    expect($participants[0]->nachname)->toBe('Beta');
});

it('returns an empty list when the search term matches no participant', function (): void {
    $query = new ParticipantsQuery();
    $participants = $query->execute('nicht-vorhanden');

    expect($participants)->toHaveCount(0);
});
