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
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\GenerateInvoicePdf;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\Request as UpsertRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\UpsertParticipant;
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
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Mail\HtmlAttachmentMail;
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
    expect($result->value()->wasCreated)->toBeTrue();

    $participant = Participant::findById($result->value()->participantId);
    expect($participant)->not->toBeNull();
    expect($participant->email)->toBe('neu@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $result->value()->participantId,
        paymentMethod: RegistrationPaymentMethod::Transfer,
    ));

    expect($registration->isSuccess())->toBeTrue();
    expect($registration->value()->onWaitingList)->toBeFalse();

    $registrationRecord = Registration::findById($registration->value()->registrationId);
    expect($registrationRecord)->not->toBeNull();
    expect($registrationRecord->status)->toBe(RegistrationStatus::Confirmed);

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->value()->registrationId)->getBytes()])->first();
    expect($payment)->not->toBeNull();

    $invoice = Invoice::whereRaw('zahlung_id = ?', [Uuid::fromString($payment->getAttribute('id'))->getBytes()])->first();
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
    expect($result->value()->wasCreated)->toBeFalse();

    $existing = $existing->fresh();
    expect($existing->vorname)->toBe('Neu');
});

it('places registration on waiting list when activity is full', function (): void {
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

    $registrationRecord = Registration::findById($waiting->value()->registrationId);
    expect($registrationRecord->status)->toBe(RegistrationStatus::WaitingList);

    $entry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($waiting->value()->registrationId)->getBytes()])->first();
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
        activityId: $freeActivity->getAttribute('id'),
        participantId: $participant->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Free,
    ));

    expect($registration->isSuccess())->toBeTrue();

    $payment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->value()->registrationId)->getBytes()])->first();
    expect($payment)->toBeNull();

    $registrationRecord = Registration::findById($registration->value()->registrationId);
    expect($registrationRecord->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);
});

it('generates a PDF for a transfer invoice', function (): void {
    $participant = TestFactory::createParticipant(email: 'rechnung@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->getAttribute('id'),
        participantId: $participant->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Transfer,
    ));

    expect($registration->value()->invoiceId)->toBeUuidString();

    $generator = new GenerateInvoicePdf();
    $result = $generator->execute(new GeneratePdfRequest($registration->value()->invoiceId));

    expect($result->isSuccess())->toBeTrue();
    expect($result->value()->content)->not->toBe('');
    expect($result->value()->filename)->toStartWith('Rechnung-R-');
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
        activityId: $this->activity->getAttribute('id'),
        participantId: $participant->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Transfer,
    ));

    $confirmation = new SendRegistrationConfirmation(new GenerateInvoicePdf());
    $result = $confirmation->execute(new ConfirmationRequest(
        registrationId: $registration->value()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $message = OutboundMessage::findById($result->value()->outboundMessageId);
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
        activityId: $freeActivity->getAttribute('id'),
        participantId: $participant->getAttribute('id'),
        paymentMethod: RegistrationPaymentMethod::Free,
    ));

    $confirmation = new SendRegistrationConfirmation(new GenerateInvoicePdf());
    $result = $confirmation->execute(new ConfirmationRequest(
        registrationId: $registration->value()->registrationId,
    ));

    expect($result->isSuccess())->toBeTrue();

    $message = OutboundMessage::findById($result->value()->outboundMessageId);
    expect($message)->not->toBeNull();
    expect($message->status)->toBe(OutboundMessageStatus::Sent);

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail): bool {
        return $mail->attachment === null;
    });
});
