<?php

declare(strict_types=1);

/**
 * Story: E1 Öffentliche Webseite / F3 Online-Anmeldung
 *
 * Geprüfte Kriterien:
 * - Bei einer neuen E-Mail wird ein neuer Participant angelegt.
 * - Bei einer bekannten E-Mail wird der bestehende Participant aktualisiert.
 * - Bei freiem Platz wird die Anmeldung mit Status CONFIRMED angelegt.
 * - Bei ausgebuchter Veranstaltung wird die Anmeldung mit Status WAITING_LIST angelegt.
 * - Bei Zahlungsart „Überweisung" wird automatisch eine Rechnung erzeugt.
 * - Bei Zahlungsart „Kostenlos" wird keine Zahlung und kein Beleg erzeugt.
 */

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\GenerateInvoicePdf;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\Request as UpsertRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\UpsertParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\CancelRegistration;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\Request as CancelRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\Request as ConfirmationRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\SendRegistrationConfirmation;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
});

it('creates a new participant and a confirmed registration for transfer payment', function (): void {
    $upsert = new UpsertParticipant();
    $result = $upsert->execute(new UpsertRequest(
        email: 'neu@example.com',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->wasCreated)->toBeTrue();

    $participant = Participant::findById($result->unwrap()->participantId);
    expect($participant)->not->toBeNull();
    expect($participant->email)->toBe('neu@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $result->unwrap()->participantId,
        paymentMethod: 'ueberweisung',
    ));

    expect($registration->isSuccess())->toBeTrue();
    expect($registration->unwrap()->onWaitingList)->toBeFalse();

    $registrationRecord = Registration::findById($registration->unwrap()->registrationId);
    expect($registrationRecord)->not->toBeNull();
    expect($registrationRecord->status)->toBe(RegistrationStatus::Confirmed);

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->unwrap()->registrationId)->getBytes()])->first();
    expect($payment)->not->toBeNull();

    $invoice = Invoice::whereRaw('zahlung_id = ?', [Uuid::fromString($payment->id)->getBytes()])->first();
    expect($invoice)->not->toBeNull();
});

it('updates an existing participant by email', function (): void {
    $existing = new Participant([
        'email' => 'bekannt@example.com',
        'vorname' => 'Alt',
        'nachname' => 'Name',
    ]);
    $existing->save();

    $upsert = new UpsertParticipant();
    $result = $upsert->execute(new UpsertRequest(
        email: 'bekannt@example.com',
        firstName: 'Neu',
        lastName: 'Name',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->wasCreated)->toBeFalse();

    $existing = $existing->fresh();
    expect($existing->vorname)->toBe('Neu');
});

it('places registration on waiting list when activity is full', function (): void {
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

    expect($waiting->isSuccess())->toBeTrue();
    expect($waiting->unwrap()->onWaitingList)->toBeTrue();

    $registrationRecord = Registration::findById($waiting->unwrap()->registrationId);
    expect($registrationRecord->status)->toBe(RegistrationStatus::WaitingList);

    $entry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($waiting->unwrap()->registrationId)->getBytes()])->first();
    expect($entry)->not->toBeNull();
});

it('does not create a payment for free registrations', function (): void {
    $freeActivity = TestFactory::createActivity(maxParticipants: 2);
    $freeActivity->preis = 0;
    $freeActivity->save();

    $participant = new Participant([
        'email' => 'kostenlos@example.com',
        'vorname' => 'Max',
        'nachname' => 'Mustermann',
    ]);
    $participant->save();

    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $freeActivity->id,
        participantId: $participant->id,
        paymentMethod: 'kostenlos',
    ));

    expect($registration->isSuccess())->toBeTrue();

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->unwrap()->registrationId)->getBytes()])->first();
    expect($payment)->toBeNull();

    $registrationRecord = Registration::findById($registration->unwrap()->registrationId);
    expect($registrationRecord->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);
});

it('generates a PDF for a transfer invoice', function (): void {
    $participant = TestFactory::createParticipant(email: 'rechnung@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'ueberweisung',
    ));

    expect($registration->unwrap()->invoiceId)->toBeUuidString();

    $generator = new GenerateInvoicePdf();
    $result = $generator->execute(new GeneratePdfRequest($registration->unwrap()->invoiceId));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->content)->not->toBe('');
    expect($result->unwrap()->filename)->toStartWith('Rechnung-R-');
});

it('sends a confirmation email and creates an outbound message for a transfer registration', function (): void {
    Mail::fake();

    $participant = TestFactory::createParticipant(
        email: 'mail@example.com',
        vorname: 'Max',
        nachname: 'Mustermann',
    );
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'ueberweisung',
    ));

    $confirmation = new SendRegistrationConfirmation(new GenerateInvoicePdf(), new SendOutboundMessage());
    $result = $confirmation->execute(new ConfirmationRequest(
        registrationId: $registration->unwrap()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $message = OutboundMessage::findById($result->unwrap()->outboundMessageId);
    expect($message)->not->toBeNull();
    expect($message->status)->toBe(OutboundMessageStatus::Sent);
    expect($message->empfaenger)->toBe('mail@example.com');
    expect($message->betreff)->toContain('Anmeldebestätigung');
    expect($message->inhalt)->toContain($this->activity->titel);
    expect($message->inhalt)->not->toContain('Gesundheitsinformation');

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail) use ($participant): bool {
        return $mail->hasTo($participant->email)
            && str_contains($mail->subjectText, 'Anmeldebestätigung')
            && $mail->attachment !== null;
    });
});

it('sends a confirmation email without attachment for a free registration', function (): void {
    Mail::fake();

    $freeActivity = TestFactory::createActivity(maxParticipants: 2);
    $freeActivity->preis = 0;
    $freeActivity->save();

    $participant = TestFactory::createParticipant(email: 'kostenlos@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $freeActivity->id,
        participantId: $participant->id,
        paymentMethod: 'kostenlos',
    ));

    $confirmation = new SendRegistrationConfirmation(new GenerateInvoicePdf(), new SendOutboundMessage());
    $result = $confirmation->execute(new ConfirmationRequest(
        registrationId: $registration->unwrap()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $message = OutboundMessage::findById($result->unwrap()->outboundMessageId);
    expect($message)->not->toBeNull();
    expect($message->status)->toBe(OutboundMessageStatus::Sent);

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail): bool {
        return $mail->attachment === null;
    });
});

it('matches an existing participant even with different email casing and surrounding spaces', function (): void {
    $existing = TestFactory::createParticipant(email: 'max.mustermann@example.com');

    $upsert = new UpsertParticipant();
    $result = $upsert->execute(new UpsertRequest(
        email: '  Max.Mustermann@Example.COM ',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->wasCreated)->toBeFalse();
    expect($result->unwrap()->participantId)->toBe((string) $existing->id);

    expect(Participant::where('email', 'max.mustermann@example.com')->count())->toBe(1);
    expect(Participant::where('email', 'like', '%@example.com')->count())->toBe(1);
});

it('allows a known email to register for another activity without a duplicate participant', function (): void {
    $otherActivity = TestFactory::createActivity(maxParticipants: 2);

    $upsert = new UpsertParticipant();
    $first = $upsert->execute(new UpsertRequest(
        email: 'wiederholung@example.com',
        firstName: 'Lena',
        lastName: 'Wiederholung',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    $register = new RegisterParticipant(app(NextNumber::class));
    $firstRegistration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $first->unwrap()->participantId,
        paymentMethod: 'bar',
    ));
    expect($firstRegistration->isSuccess())->toBeTrue();

    // Zweite Anmeldung über die Webseite mit derselben E-Mail: die Stammdaten
    // werden aktualisiert, kein zweiter Teilnehmerdatensatz entsteht.
    $second = $upsert->execute(new UpsertRequest(
        email: 'Wiederholung@example.com',
        firstName: 'Lena',
        lastName: 'Wiederholung',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    expect($second->isSuccess())->toBeTrue();
    expect($second->unwrap()->wasCreated)->toBeFalse();
    expect($second->unwrap()->participantId)->toBe($first->unwrap()->participantId);

    $secondRegistration = $register->execute(new RegisterRequest(
        activityId: $otherActivity->id,
        participantId: $second->unwrap()->participantId,
        paymentMethod: 'bar',
    ));

    expect($secondRegistration->isSuccess())->toBeTrue();
    expect(Participant::where('email', 'wiederholung@example.com')->count())->toBe(1);
});

it('allows re-registration for the same activity after the first registration was cancelled', function (): void {
    $participant = TestFactory::createParticipant(email: 'erneut@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $first = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));
    expect($first->isSuccess())->toBeTrue();

    app(CancelRegistration::class)->execute(new CancelRequest(
        registrationId: $first->unwrap()->registrationId,
    ));

    $second = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    expect($second->isSuccess())->toBeTrue();

    $record = Registration::findById($second->unwrap()->registrationId);
    expect($record->status)->toBe(RegistrationStatus::Confirmed);
});

it('rejects a second registration for the same activity with a clear message', function (): void {
    Mail::fake();

    $participant = TestFactory::createParticipant(email: 'doppel@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    Livewire::test(\Yoga\Modules\Webseite\Ui\Registration\RegisterForActivity::class, ['slug' => $this->activity->slug])
        ->set('firstName', 'Max')
        ->set('lastName', 'Doppel')
        ->set('email', 'doppel@example.com')
        ->set('paymentMethod', 'bar')
        ->call('submit')
        ->assertSee('Sie sind für diese Veranstaltung bereits angemeldet.');
});
