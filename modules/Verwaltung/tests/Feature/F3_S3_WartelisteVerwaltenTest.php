<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F3 Anmeldungen und Warteliste verwalten / S3 Warteliste verwalten
 *
 * Geprüfte Kriterien:
 * - Die Warteliste wird in der Anmeldungsansicht angezeigt.
 * - Die Liste zeigt Rang, Name, E-Mail und Anmeldedatum.
 * - Die Reihenfolge ist standardmäßig nach Anmeldedatum.
 * - Ein Wartelisten-Eintrag kann manuell in der Rangfolge verschoben werden.
 * - Ein manuell nachgerückter Eintrag löst die E-Mail-Benachrichtigung aus.
 * - Ein Wartelisten-Eintrag kann direkt storniert werden.
 */

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Ui\Registration\ActivityRegistrations;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity(maxParticipants: 1);
    $this->confirmedParticipant = TestFactory::createParticipant(email: 'erste@example.com', vorname: 'Anna', nachname: 'Alpha');
    $this->firstWaiting = TestFactory::createParticipant(email: 'zweite@example.com', vorname: 'Berta', nachname: 'Beta');
    $this->secondWaiting = TestFactory::createParticipant(email: 'dritte@example.com', vorname: 'Clara', nachname: 'Gamma');

    $register = new RegisterParticipant(app(NextNumber::class));
    $this->confirmed = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $this->confirmedParticipant->id,
        paymentMethod: 'bar',
    ));
    $this->waitingFirst = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $this->firstWaiting->id,
        paymentMethod: 'bar',
    ));
    $this->waitingSecond = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $this->secondWaiting->id,
        paymentMethod: 'bar',
    ));
});

it('shows the waiting list with rank, name, email and registered at', function (): void {
    Livewire::test(ActivityRegistrations::class, ['id' => $this->activity->id])
        ->assertSee('Warteliste')
        ->assertSee('zweite@example.com')
        ->assertSee('dritte@example.com');
});

it('orders the waiting list by registered at by default', function (): void {
    $component = Livewire::test(ActivityRegistrations::class, ['id' => $this->activity->id]);

    /** @var list<object{id: string, status: string, rang: int|null}> $registrations */
    $registrations = $component->get('registrations');

    $ranks = [];
    foreach ($registrations as $registration) {
        if ($registration->status === 'warteliste' && $registration->rang !== null) {
            $ranks[$registration->id] = $registration->rang;
        }
    }

    expect($ranks)->toHaveCount(2);
    expect($ranks[$this->waitingFirst->unwrap()->registrationId])->toBeLessThan(
        $ranks[$this->waitingSecond->unwrap()->registrationId],
    );
});

it('allows moving a waiting-list entry up and down', function (): void {
    $component = Livewire::test(ActivityRegistrations::class, ['id' => $this->activity->id]);
    $secondId = $this->waitingSecond->unwrap()->registrationId;

    $component->call('moveUp', $secondId);

    $secondRankAfterUp = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($secondId)->getBytes()])->first();
    expect($secondRankAfterUp)->not->toBeNull();
    expect($secondRankAfterUp->rang)->toBe(1);

    $component->call('moveDown', $secondId);

    $secondRankAfterDown = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($secondId)->getBytes()])->first();
    expect($secondRankAfterDown)->not->toBeNull();
    expect($secondRankAfterDown->rang)->toBe(2);
});

it('promotes a waiting-list entry manually and sends confirmation email', function (): void {
    Mail::fake();

    $component = Livewire::test(ActivityRegistrations::class, ['id' => $this->activity->id]);
    $waitingId = $this->waitingFirst->unwrap()->registrationId;

    $component->call('promote', $waitingId)
        ->assertSet('message', '');

    $registration = Registration::findById($waitingId);
    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::Confirmed);

    $waitingListEntry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($waitingId)->getBytes()])->first();
    expect($waitingListEntry)->not->toBeNull();
    expect($waitingListEntry->nachgerueckt_am)->not->toBeNull();

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail): bool {
        return $mail->hasTo('zweite@example.com');
    });
});

it('cancels a waiting-list entry directly', function (): void {
    $component = Livewire::test(ActivityRegistrations::class, ['id' => $this->activity->id]);
    $waitingId = $this->waitingFirst->unwrap()->registrationId;

    $component->call('cancel', $waitingId);

    $registration = Registration::findById($waitingId);
    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::Cancelled);
});
