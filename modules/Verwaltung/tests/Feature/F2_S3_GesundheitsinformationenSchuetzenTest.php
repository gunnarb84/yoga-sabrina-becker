<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F2 Teilnehmer verwalten / S3 Gesundheitsinformationen schützen
 *
 * Geprüfte Kriterien:
 * - Das Feld healthNotes ist in der Teilnehmerliste nicht sichtbar.
 * - Ohne gesetzte healthNotesConsent kann healthNotes nicht gespeichert werden.
 * - healthNotes werden nicht in E-Mails an Teilnehmer/innen eingebettet.
 */

use Illuminate\Support\Facades\Mail;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Application\Participant\Participants\ParticipantsQuery;
use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\Request as UpdateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\UpdateParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\Request as SendConfirmationRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\SendRegistrationConfirmation;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

it('does not expose health notes in the participant list query', function (): void {
    $participant = TestFactory::createParticipant(email: 'gesundheit@example.com');

    $update = new UpdateParticipant();
    $update->execute(new UpdateParticipantRequest(
        participantId: $participant->id,
        email: $participant->email,
        firstName: $participant->vorname,
        lastName: $participant->nachname,
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: 'Chronische Rückenschmerzen',
        healthNotesConsent: true,
    ));

    $query = new ParticipantsQuery();
    $participants = $query->execute();

    expect($participants)->toHaveCount(1);
    expect($participants[0])->not->toHaveProperty('healthNotes');
    expect($participants[0])->not->toHaveProperty('gesundheitsinformationen');
});

it('does not store health notes without consent', function (): void {
    $participant = TestFactory::createParticipant(email: 'ohne-einwilligung@example.com');

    $update = new UpdateParticipant();
    $result = $update->execute(new UpdateParticipantRequest(
        participantId: $participant->id,
        email: $participant->email,
        firstName: $participant->vorname,
        lastName: $participant->nachname,
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: 'Allergie gegen Latex',
        healthNotesConsent: false,
    ));

    expect($result->isSuccess())->toBeTrue();

    $reload = Participant::findById($participant->id);
    expect($reload)->not->toBeNull();
    expect($reload->gesundheitsinformationen)->toBeNull();
    expect($reload->gesundheitsinformationen_einwilligung)->toBeFalse();
});

it('does not include health notes in the registration confirmation email', function (): void {
    Mail::fake();

    $activity = TestFactory::createActivity();
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');

    $update = new UpdateParticipant();
    $update->execute(new UpdateParticipantRequest(
        participantId: $participant->id,
        email: $participant->email,
        firstName: $participant->vorname,
        lastName: $participant->nachname,
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: 'Bandscheibenvorfall',
        healthNotesConsent: true,
    ));

    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $confirmation = app(SendRegistrationConfirmation::class);
    $confirmation->execute(new SendConfirmationRequest($registration->unwrap()->registrationId));

    $message = OutboundMessage::query()
        ->whereRaw('empfaenger = ?', [$participant->email])
        ->first();

    expect($message)->not->toBeNull();
    expect($message->inhalt)->not->toContain('Bandscheibenvorfall');

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail): bool {
        return str_contains($mail->htmlContent, 'Bandscheibenvorfall') === false;
    });
});
