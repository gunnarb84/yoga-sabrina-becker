<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F3 Anmeldungen und Warteliste verwalten /
 *        S4 Herkunft einer Anmeldung
 *
 * Geprüfte Kriterien:
 * - Jede Anmeldung trägt die Herkunft source mit dem Wert Webseite oder
 *   Verwaltung.
 * - Anmeldungen aus dem Online-Anmeldeprozess erhalten die Herkunft Webseite.
 * - Anmeldungen, die über die Verwaltung angelegt werden, erhalten die
 *   Herkunft Verwaltung.
 * - Die Liste aller Anmeldungen und die Veranstaltungs-Anmeldeliste zeigen
 *   die Spalte „Herkunft“ mit dem Wert der Anmeldung.
 */

use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment\RecordWalkInCashPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment\Request as RecordWalkInRequest;
use Yoga\Modules\Verwaltung\Application\Registration\AllRegistrations\AllRegistrationsQuery;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegistrationsByActivity\RegistrationsByActivityQuery;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationSource;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Registration\ActivityRegistrations;
use Yoga\Modules\Verwaltung\Ui\Registration\AllRegistrations;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity();
});

/**
 * Legt eine Anmeldung über den Online-Anmeldeprozess an (Standard-Herkunft).
 */
function meldeOnlineAn(object $activity, string $email, string $vorname, string $nachname): string
{
    $participant = TestFactory::createParticipant(email: $email, vorname: $vorname, nachname: $nachname);
    $register = new RegisterParticipant(app(NextNumber::class));

    return $register->execute(new RegisterRequest(
        activityId: $activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ))->unwrap()->registrationId;
}

it('carries the source Webseite or Verwaltung on every registration', function (): void {
    $webId = meldeOnlineAn($this->activity, 'web@example.com', 'Wera', 'Webseite');
    $participant = TestFactory::createParticipant(email: 'verwaltung@example.com', vorname: 'Vera', nachname: 'Verwaltung');
    $register = new RegisterParticipant(app(NextNumber::class));
    $verwaltungId = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
        source: RegistrationSource::Administration->value,
    ))->unwrap()->registrationId;

    expect(Registration::findById($webId)->herkunft)->toBe(RegistrationSource::Website);
    expect(Registration::findById($verwaltungId)->herkunft)->toBe(RegistrationSource::Administration);
});

it('marks online registrations as Webseite by default', function (): void {
    $registrationId = meldeOnlineAn($this->activity, 'online@example.com', 'Olga', 'Online');

    expect(Registration::findById($registrationId)->herkunft)->toBe(RegistrationSource::Website);
});

it('marks walk-in registrations as Verwaltung', function (): void {
    $operation = new RecordWalkInCashPayment(
        new \Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment(
            app(NextNumber::class),
            new \Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf(
                new \Yoga\Modules\Verwaltung\Application\CashReceipt\AmountInWords(),
            ),
            new \Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage(),
        ),
    );

    $result = $operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: null,
        amount: '45.00',
    ));
    expect($result->isSuccess())->toBeTrue();

    $registration = Registration::findById(
        \Yoga\Modules\Verwaltung\Domain\Payment\Payment::findById($result->unwrap()->paymentId)->anmeldung_id,
    );
    expect($registration->herkunft)->toBe(RegistrationSource::Administration);
});

it('shows the Herkunft column in the list of all registrations', function (): void {
    meldeOnlineAn($this->activity, 'online@example.com', 'Olga', 'Online');

    TestFactory::createParticipant(email: 'vera@example.com', vorname: 'Vera', nachname: 'Verwaltung');
    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: \Yoga\Modules\Verwaltung\Domain\Participant\Participant::where('email', 'vera@example.com')->first()->id,
        paymentMethod: 'bar',
        source: RegistrationSource::Administration->value,
    ));

    $registrations = (new AllRegistrationsQuery())->execute();

    expect($registrations)->toHaveCount(2);

    $herkunft = [];
    foreach ($registrations as $registration) {
        $herkunft[$registration->teilnehmer_name] = $registration->herkunft;
    }
    expect($herkunft['Olga Online'])->toBe('webseite');
    expect($herkunft['Vera Verwaltung'])->toBe('verwaltung');

    Livewire::test(AllRegistrations::class)
        ->assertSee('Herkunft')
        ->assertSee('Webseite')
        ->assertSee('Verwaltung');
});

it('shows the Herkunft column in the activity registration list', function (): void {
    meldeOnlineAn($this->activity, 'online@example.com', 'Olga', 'Online');

    $registrations = (new RegistrationsByActivityQuery())->execute($this->activity->id);

    expect($registrations)->toHaveCount(1);
    expect($registrations[0]->herkunft)->toBe('webseite');

    Livewire::test(ActivityRegistrations::class, ['id' => $this->activity->id])
        ->assertSee('Herkunft')
        ->assertSee('Webseite');
});
