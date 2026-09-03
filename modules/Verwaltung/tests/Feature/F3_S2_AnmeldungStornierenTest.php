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

use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\CancelRegistration;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\Request as CancelRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
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
        activityId: $this->activity->getAttribute('id'),
        participantId: $participant->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));

    expect($response->isSuccess())->toBeTrue();

    $cancel = new CancelRegistration(app(NextNumber::class));
    $result = $cancel->execute(new CancelRequest(
        registrationId: $response->value()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $registration = Registration::findById($response->value()->registrationId);
    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::Cancelled);
});

it('fails with not_found for a non-existing registration', function (): void {
    $cancel = new CancelRegistration(app(NextNumber::class));
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
        activityId: $this->activity->getAttribute('id'),
        participantId: $participant->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));

    $cancel = new CancelRegistration(app(NextNumber::class));
    $cancel->execute(new CancelRequest(registrationId: $response->value()->registrationId));
    $second = $cancel->execute(new CancelRequest(registrationId: $response->value()->registrationId));

    expect($second->isFailure())->toBeTrue();
    expect($second->error()['code'])->toBe('registration.already_cancelled');
});

it('promotes the first waiting-list entry when a confirmed registration is cancelled', function (): void {
    $first = TestFactory::createParticipant(email: 'erste@example.com');
    $second = TestFactory::createParticipant(email: 'zweite@example.com');
    $third = TestFactory::createParticipant(email: 'dritte@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $confirmed = $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $first->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $second->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));
    $waiting = $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $third->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));

    expect($waiting->value()->onWaitingList)->toBeTrue();

    $cancel = new CancelRegistration(app(NextNumber::class));
    $cancel->execute(new CancelRequest(registrationId: $confirmed->value()->registrationId));

    $promoted = Registration::findById($waiting->value()->registrationId);
    expect($promoted)->not->toBeNull();
    expect($promoted->status)->toBe(RegistrationStatus::Confirmed);

    $waitingListEntry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($promoted->getAttribute('id'))->getBytes()])->first();
    expect($waitingListEntry)->not->toBeNull();
    expect($waitingListEntry->nachgerueckt_am)->not->toBeNull();
});

it('does not promote another entry when a waiting-list registration is cancelled', function (): void {
    $first = TestFactory::createParticipant(email: 'erste@example.com');
    $second = TestFactory::createParticipant(email: 'zweite@example.com');
    $third = TestFactory::createParticipant(email: 'dritte@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $first->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $second->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));
    $waiting = $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $third->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));

    $cancel = new CancelRegistration(app(NextNumber::class));
    $cancel->execute(new CancelRequest(registrationId: $waiting->value()->registrationId));

    $firstRegistration = Registration::query()
        ->whereRaw('teilnehmer_id = ?', [Uuid::fromString($first->getAttribute('id'))->getBytes()])
        ->first();

    expect($firstRegistration)->not->toBeNull();
    expect($firstRegistration->status)->toBe(RegistrationStatus::Confirmed);
});
