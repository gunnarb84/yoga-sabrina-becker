<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F3 Anmeldungen und Warteliste verwalten / S2 Anmeldung stornieren
 *
 * Geprüfte Kriterien:
 * - Der Vorgang CancelRegistration setzt den Status auf CANCELLED.
 * - CancelRegistration scheitert mit NOT_FOUND, wenn die Anmeldung nicht existiert.
 * - CancelRegistration scheitert mit ALREADY_CANCELLED, wenn der Status bereits CANCELLED ist.
 * - Bei einer Stornierung eines festen Platzes wird der erste Eintrag der Warteliste
 *   automatisch nachgerückt.
 */

use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\CancelRegistration;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\Request as CancelRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
});

it('sets the registration status to cancelled', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $response = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    expect($response->isSuccess())->toBeTrue();

    $cancel = app(CancelRegistration::class);
    $result = $cancel->execute(new CancelRequest(
        registrationId: $response->unwrap()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $registration = Registration::findById($response->unwrap()->registrationId);
    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::Cancelled);
});

it('fails with not_found for a non-existing registration', function (): void {
    $cancel = app(CancelRegistration::class);
    $result = $cancel->execute(new CancelRequest(
        registrationId: '018e1234-5678-7abc-8def-0123456789ab',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('registration.not_found');
});

it('fails with already_cancelled when the registration is already cancelled', function (): void {
    $participant = TestFactory::createParticipant(email: 'teilnehmer@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $response = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $cancel = app(CancelRegistration::class);
    $cancel->execute(new CancelRequest(registrationId: $response->unwrap()->registrationId));
    $second = $cancel->execute(new CancelRequest(registrationId: $response->unwrap()->registrationId));

    expect($second->isFailure())->toBeTrue();
    expect($second->error()['code'])->toBe('registration.already_cancelled');
});

it('promotes the first waiting-list entry when a confirmed registration is cancelled', function (): void {
    Mail::fake();

    $first = TestFactory::createParticipant(email: 'erste@example.com');
    $second = TestFactory::createParticipant(email: 'zweite@example.com');
    $third = TestFactory::createParticipant(email: 'dritte@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $confirmed = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $first->id,
        paymentMethod: 'bar',
    ));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $second->id,
        paymentMethod: 'bar',
    ));
    $waiting = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $third->id,
        paymentMethod: 'bar',
    ));

    expect($waiting->unwrap()->onWaitingList)->toBeTrue();

    $cancel = app(CancelRegistration::class);
    $cancel->execute(new CancelRequest(registrationId: $confirmed->unwrap()->registrationId));

    $promoted = Registration::findById($waiting->unwrap()->registrationId);
    expect($promoted)->not->toBeNull();
    expect($promoted->status)->toBe(RegistrationStatus::Confirmed);

    $waitingListEntry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($promoted->id)->getBytes()])->first();
    expect($waitingListEntry)->not->toBeNull();
    expect($waitingListEntry->nachgerueckt_am)->not->toBeNull();

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail): bool {
        return $mail->hasTo('dritte@example.com');
    });
});

it('does not promote another entry when a waiting-list registration is cancelled', function (): void {
    $first = TestFactory::createParticipant(email: 'erste@example.com');
    $second = TestFactory::createParticipant(email: 'zweite@example.com');
    $third = TestFactory::createParticipant(email: 'dritte@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $first->id,
        paymentMethod: 'bar',
    ));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $second->id,
        paymentMethod: 'bar',
    ));
    $waiting = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $third->id,
        paymentMethod: 'bar',
    ));

    $cancel = app(CancelRegistration::class);
    $cancel->execute(new CancelRequest(registrationId: $waiting->unwrap()->registrationId));

    $firstRegistration = Registration::query()
        ->whereRaw('teilnehmer_id = ?', [Uuid::fromString($first->id)->getBytes()])
        ->first();

    expect($firstRegistration)->not->toBeNull();
    expect($firstRegistration->status)->toBe(RegistrationStatus::Confirmed);
});
