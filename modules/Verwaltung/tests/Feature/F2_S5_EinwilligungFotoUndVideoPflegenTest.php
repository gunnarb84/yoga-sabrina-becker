<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F2 Teilnehmer verwalten / S5 Einwilligung Foto- und Videoaufnahmen pflegen
 *
 * Geprüfte Kriterien:
 * - UpdateParticipant speichert die Foto- und Video-Einwilligung mit den Zeitpunkten.
 * - Beim Zurücksetzen einer Einwilligung wird der Zeitpunkt geleert.
 * - Die Speicherung gelingt auch ohne gesetzte Einwilligungen (Freiwilligkeit).
 * - Widerrufsvermerk (Datum und Text) wird erfasst und ist optional.
 * - Die Einwilligung ist unabhängig vom Geburtsdatum erfassbar (auch Minderjährige).
 */

use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\Request as UpdateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\UpdateParticipant;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Tests\TestFactory;

beforeEach(function (): void {
    $this->participant = TestFactory::createParticipant(email: 'einwilligung@example.com');
});

it('saves photo and video consents with their dates', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'einwilligung@example.com',
        firstName: 'Lena',
        lastName: 'Musterfrau',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
        photoConsent: true,
        photoConsentAt: '2026-09-20',
        videoConsent: true,
        videoConsentAt: '2026-09-20',
    ));

    expect($result->isSuccess())->toBeTrue();

    $updated = Participant::findById($this->participant->id);
    expect($updated->foto_einwilligung)->toBeTrue();
    expect($updated->foto_einwilligung_am->format('Y-m-d'))->toBe('2026-09-20');
    expect($updated->video_einwilligung)->toBeTrue();
    expect($updated->video_einwilligung_am->format('Y-m-d'))->toBe('2026-09-20');
});

it('clears the consent date when a consent is revoked', function (): void {
    $operation = new UpdateParticipant();
    $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'einwilligung@example.com',
        firstName: 'Lena',
        lastName: 'Musterfrau',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
        photoConsent: true,
        photoConsentAt: '2026-09-20',
    ));

    $second = (new UpdateParticipant())->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'einwilligung@example.com',
        firstName: 'Lena',
        lastName: 'Musterfrau',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
        photoConsent: false,
        photoConsentAt: null,
    ));

    expect($second->isSuccess())->toBeTrue();

    $updated = Participant::findById($this->participant->id);
    expect($updated->foto_einwilligung)->toBeFalse();
    expect($updated->foto_einwilligung_am)->toBeNull();
});

it('saves without any consent because the consent is voluntary', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'einwilligung@example.com',
        firstName: 'Lena',
        lastName: 'Musterfrau',
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

    $updated = Participant::findById($this->participant->id);
    expect($updated->foto_einwilligung)->toBeFalse();
    expect($updated->video_einwilligung)->toBeFalse();
});

it('saves the revocation note with date and text', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'einwilligung@example.com',
        firstName: 'Lena',
        lastName: 'Musterfrau',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
        healthNotesConsent: false,
        photoConsent: false,
        photoConsentAt: null,
        videoConsent: false,
        videoConsentAt: null,
        revocationAt: '2026-09-20',
        revocationNote: 'per E-Mail, betrifft Fotos',
    ));

    expect($result->isSuccess())->toBeTrue();

    $updated = Participant::findById($this->participant->id);
    expect($updated->widerruf_am->format('Y-m-d'))->toBe('2026-09-20');
    expect($updated->widerrufsvermerk)->toBe('per E-Mail, betrifft Fotos');
});

it('records the consent for a minor with a signed paper form', function (): void {
    $operation = new UpdateParticipant();
    $result = $operation->execute(new UpdateParticipantRequest(
        participantId: $this->participant->id,
        email: 'einwilligung@example.com',
        firstName: 'Mia',
        lastName: 'Kind',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: '2012-03-01',
        healthNotes: null,
        healthNotesConsent: false,
        photoConsent: true,
        photoConsentAt: '2026-09-20',
        videoConsent: false,
        videoConsentAt: null,
    ));

    expect($result->isSuccess())->toBeTrue();

    $updated = Participant::findById($this->participant->id);
    expect($updated->foto_einwilligung)->toBeTrue();
    expect($updated->foto_einwilligung_am->format('Y-m-d'))->toBe('2026-09-20');
});
