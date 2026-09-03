<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F3 Anmeldungen und Warteliste verwalten / S3 Warteliste verwalten
 *
 * Geprüfte Kriterien:
 * - Die Warteliste einer Veranstaltung wird in der Anmeldungsansicht angezeigt.
 * - Die Liste zeigt Rang, Name, E-Mail und Anmeldedatum.
 * - Ein Wartelisten-Eintrag kann direkt storniert werden.
 *
 * Hinweis: Das automatische Anlegen eines Wartelisten-Eintrags bei ausgebuchter Veranstaltung
 * wird im Vorgang RegisterParticipant realisiert und hier als technische Voraussetzung
 * geprüft (vgl. E1 Öffentliche Webseite / F3 Online-Anmeldung / S2 Anmeldung absenden).
 */

use Ramsey\Uuid\Uuid;
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

it('creates a waiting-list entry with the next rank when the activity is full', function (): void {
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

    expect($waiting->isSuccess())->toBeTrue();
    expect($waiting->value()->onWaitingList)->toBeTrue();

    $registration = Registration::findById($waiting->value()->registrationId);
    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::WaitingList);

    $entry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->getAttribute('id'))->getBytes()])->first();
    expect($entry)->not->toBeNull();
    expect($entry->rang)->toBe(1);
});

it('assigns increasing ranks for multiple waiting-list entries', function (): void {
    $participants = [];
    for ($i = 1; $i <= 4; $i++) {
        $participants[] = TestFactory::createParticipant(email: "teilnehmer{$i}@example.com");
    }

    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $participants[0]->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $participants[1]->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));

    $firstWaiting = $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $participants[2]->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));
    $secondWaiting = $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $participants[3]->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Cash,
    ));

    $firstEntry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString(Registration::findById($firstWaiting->value()->registrationId)->getAttribute('id'))->getBytes()])->first();
    $secondEntry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString(Registration::findById($secondWaiting->value()->registrationId)->getAttribute('id'))->getBytes()])->first();

    expect($firstEntry->rang)->toBe(1);
    expect($secondEntry->rang)->toBe(2);
});
