<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F2 Teilnehmer verwalten / S2 Teilnehmer bearbeiten
 *
 * Geprüfte Kriterien:
 * - UpdateParticipant speichert Änderungen an den Stammdaten eines Teilnehmers/einer Teilnehmerin.
 * - Pflichtfelder firstName, lastName und email werden geprüft.
 * - Die E-Mail muss ein gültiges Format haben.
 * - Bei Änderung der E-Mail auf eine bereits verwendete Adresse scheitert der Vorgang.
 * - Gesundheitsinformationen werden nur gespeichert, wenn die Einwilligung gesetzt ist.
 */

use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\Request as UpdateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\UpdateParticipant;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Tests\TestFactory;

beforeEach(function (): void {
    $this->participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
});

it('updates participant data', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'neu@example.com',
        firstName: 'Sabine',
        lastName: 'Musterfrau',
        addressLine1: 'Musterstraße 1',
        addressLine2: null,
        postalCode: '12345',
        city: 'Musterstadt',
        phone: '0123456789',
        dateOfBirth: '1990-05-15',
        healthNotes: 'Rückenprobleme',
        healthNotesConsent: true,
    ));

    expect($result->isSuccess())->toBeTrue();

    $updated = Participant::findById($this->participant->id);
    expect($updated)->not->toBeNull();
    expect($updated->email)->toBe('neu@example.com');
    expect($updated->vorname)->toBe('Sabine');
    expect($updated->nachname)->toBe('Musterfrau');
    expect($updated->adresszeile_1)->toBe('Musterstraße 1');
    expect($updated->postleitzahl)->toBe('12345');
    expect($updated->stadt)->toBe('Musterstadt');
    expect($updated->telefon)->toBe('0123456789');
    expect($updated->geburtsdatum)->not->toBeNull();
    expect($updated->gesundheitsinformationen)->toBe('Rückenprobleme');
    expect($updated->gesundheitsinformationen_einwilligung)->toBeTrue();
});

it('fails with not_found for a non-existing participant', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: '018e1234-5678-7abc-8def-0123456789ab',
        email: 'test@example.com',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('participant.not_found');
});

it('fails when first name is empty', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'teilnehmer@example.com',
        firstName: '',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('participant.first_name_empty');
});

it('fails when last name is empty', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'teilnehmer@example.com',
        firstName: 'Max',
        lastName: '',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('participant.last_name_empty');
});

it('fails when email is empty', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: '',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('participant.email_empty');
});

it('fails when email is invalid', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'keine-gueltige-email',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('participant.email_invalid');
});

it('fails when email already belongs to another participant', function (): void {
    TestFactory::createParticipant(email: 'anderer@example.com');

    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'anderer@example.com',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('participant.email_already_exists');
});

it('allows keeping the same email', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'teilnehmer@example.com',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
    ));

    expect($result->isSuccess())->toBeTrue();
});

it('does not store health notes without consent', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'teilnehmer@example.com',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: 'Rückenprobleme',
        healthNotesConsent: false,
    ));

    expect($result->isSuccess())->toBeTrue();

    $updated = Participant::findById($this->participant->id);
    expect($updated->gesundheitsinformationen)->toBeNull();
    expect($updated->gesundheitsinformationen_einwilligung)->toBeFalse();
});
