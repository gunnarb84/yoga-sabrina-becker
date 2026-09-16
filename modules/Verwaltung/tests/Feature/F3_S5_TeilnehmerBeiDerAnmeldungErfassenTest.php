<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F3 Anmeldungen und Warteliste verwalten /
 *        S5 Teilnehmer bei der Anmeldung erfassen
 *
 * Geprüfte Kriterien:
 * - In der Maske „Teilnehmer anmelden" ist die Schaltfläche „Neuen Teilnehmer
 *   erfassen" sichtbar.
 * - Nach dem Einschalten der Erfassung werden die Felder „Vorname" und
 *   „Nachname" als Pflichtfelder sowie „E-Mail" und „Telefon" als optionale
 *   Felder angezeigt; die Teilnehmerauswahl ist dann nicht sichtbar.
 * - Der Vorgang CreateParticipant legt beim Absenden einen neuen Participant
 *   an und der Vorgang RegisterParticipant meldet ihn mit der gewählten
 *   Zahlungsart und der Herkunft Verwaltung zur Veranstaltung an.
 * - Scheitert das Anlegen des Teilnehmers, wird keine Anmeldung angelegt und
 *   die Meldung des Vorgangs wird in der Maske angezeigt.
 * - Nach erfolgreicher Anmeldung sind die Erfassungsfelder geleert, die
 *   Erfassung ist geschlossen und der neue Teilnehmer ist in der
 *   Teilnehmerliste sichtbar.
 */

use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationSource;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Registration\RegisterParticipant;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity(maxParticipants: 12);
});

it('shows the toggle and hides the participant select while capturing a new participant', function (): void {
    $component = Livewire::test(RegisterParticipant::class, ['id' => $this->activity->id])
        ->assertSee('Neuen Teilnehmer erfassen')
        ->assertSee('Teilnehmer')
        ->assertDontSee('Vorname *');

    $component->call('toggleNewParticipant')
        ->assertSee('Vorname *')
        ->assertSee('Nachname *')
        ->assertSee('E-Mail')
        ->assertSee('Telefon')
        ->assertDontSee('Bitte wählen');
});

it('creates the participant and registers them for the activity in one step', function (): void {
    Livewire::test(RegisterParticipant::class, ['id' => $this->activity->id])
        ->call('toggleNewParticipant')
        ->set('newFirstName', 'Lena')
        ->set('newLastName', 'Neu')
        ->set('newEmail', 'lena.neu@example.com')
        ->set('newPhone', '0170 123456')
        ->set('paymentMethod', 'bar')
        ->call('register')
        ->assertSee('Die Anmeldung wurde durchgeführt.');

    $participant = Participant::where('email', 'lena.neu@example.com')->first();
    expect($participant)->not->toBeNull();
    expect($participant->vorname)->toBe('Lena');
    expect($participant->telefon)->toBe('0170 123456');

    $registration = Registration::whereRaw(
        'teilnehmer_id = ?',
        [Uuid::fromString($participant->id)->getBytes()],
    )->first();

    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::Confirmed);
    expect($registration->herkunft)->toBe(RegistrationSource::Administration);
});

it('leaves the registration out when participant creation fails', function (): void {
    TestFactory::createParticipant(email: 'doppelt@example.com');

    Livewire::test(RegisterParticipant::class, ['id' => $this->activity->id])
        ->call('toggleNewParticipant')
        ->set('newFirstName', 'Max')
        ->set('newLastName', 'Doppelt')
        ->set('newEmail', 'doppelt@example.com')
        ->call('register')
        ->assertSee('Fehler beim Anlegen des Teilnehmers');

    expect(Registration::count())->toBe(0);
});

it('clears the capture fields and lists the new participant afterwards', function (): void {
    $component = Livewire::test(RegisterParticipant::class, ['id' => $this->activity->id])
        ->call('toggleNewParticipant')
        ->set('newFirstName', 'Lena')
        ->set('newLastName', 'Erneut')
        ->set('newEmail', 'erneut@example.com')
        ->call('register')
        ->assertSee('Die Anmeldung wurde durchgeführt.');

    expect($component->get('showNewParticipant'))->toBeFalse();
    expect($component->get('newFirstName'))->toBe('');
    expect($component->get('newEmail'))->toBe('');

    $participants = $component->get('participants');
    $names = array_map(fn (object $p): string => $p->vorname.' '.$p->nachname, $participants);
    expect($names)->toContain('Lena Erneut');
});
