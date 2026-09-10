<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F2 Teilnehmer verwalten /
 *        S4 Teilnehmer ohne E-Mail-Adresse
 *
 * Geprüfte Kriterien:
 * - Eine Teilnehmerin/ein Teilnehmer kann ohne email angelegt werden.
 * - Das Feld email einer Teilnehmerin/eines Teilnehmers kann leer bleiben.
 * - Der Teilnehmerliste wird angezeigt, wenn keine E-Mail-Adresse vorhanden ist.
 */

use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\CreateParticipant as CreateParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\Request as CreateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\Participants\ParticipantsQuery;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Participant\ParticipantList;

beforeEach(function (): void {
    $this->operation = new CreateParticipantOperation();
});

it('creates a participant without an email address', function (): void {
    $result = $this->operation->execute(new CreateParticipantRequest(
        email: null,
        firstName: 'Lauf',
        lastName: 'Kundin',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    expect($result->isSuccess())->toBeTrue();

    $participant = Participant::findById($result->unwrap()->participantId);
    expect($participant)->not->toBeNull();
    expect($participant->email)->toBeNull();
});

it('keeps the email field empty for a participant without email', function (): void {
    $result = $this->operation->execute(new CreateParticipantRequest(
        email: null,
        firstName: 'Lauf',
        lastName: 'Kundin',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    $query = new ParticipantsQuery();
    $participants = $query->execute();

    expect($participants)->toHaveCount(1);
    expect($participants[0]->id)->toBe($result->unwrap()->participantId);
    expect($participants[0]->email)->toBe('');
});

it('marks the missing email address in the participant list', function (): void {
    $mit = TestFactory::createParticipant(email: 'da@example.com', vorname: 'Anna', nachname: 'Adresse');
    $ohne = TestFactory::createParticipant(email: '', vorname: 'Berta', nachname: 'Blank');

    $component = Livewire::test(ParticipantList::class)
        ->assertSee('da@example.com')
        ->assertSee('—');

    expect($mit->email)->toBe('da@example.com');
    expect($ohne->email)->toBe('');
});
