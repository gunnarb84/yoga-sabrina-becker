<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S8 Bareinnahme ohne Anmeldung erfassen
 *
 * Geprüfte Kriterien:
 * - Der Vorgang RecordWalkInCashPayment legt zu einer Veranstaltung
 *   Teilnehmer/in, Anmeldung, Payment mit der Methode CASH und einen
 *   CashReceipt an.
 * - Scheitern mit ACTIVITY_NOT_FOUND bei unbekannter Veranstaltung.
 * - Scheitern mit PARTICIPANT_NAME_REQUIRED bei leerem Vor- oder Nachnamen.
 * - Scheitern mit PARTICIPANT_EMAIL_INVALID bei ungültigem E-Mail-Format.
 * - Scheitern mit AMOUNT_INVALID bei fehlendem oder nicht positivem Betrag.
 * - Scheitern mit REGISTRATION_ALREADY_EXISTS bei bestehender nicht
 *   stornierter Anmeldung derselben Person zur selben Veranstaltung.
 * - Vorhandene Teilnehmerin/vorhandener Teilnehmer mit exakt übereinstimmendem
 *   Vor- und Nachnamen wird wiederverwendet, ohne die Stammdaten zu ändern.
 * - Die angelegte Anmeldung erhält Status Bestätigt, Zahlungsart Bar und
 *   Herkunft Verwaltung, ohne Kapazitäts- oder Wartelistenprüfung.
 * - Der Zahlungsstatus der angelegten Anmeldung wird auf „bezahlt“ gesetzt.
 * - Die Barquittung wird bei vorhandener E-Mail-Adresse automatisch per
 *   E-Mail versandt; ohne E-Mail-Adresse entsteht nur der Beleg.
 * - Mit eingeschalteter Nachpflege erstellt der Vorgang den CashReceipt mit
 *   der vorgegebenen Belegnummer und dem vorgegebenen Ausstellungsdatum;
 *   ohne Nachpflege mit der nächsten Nummer aus dem Nummernkreis und dem
 *   Erfassungsdatum.
 * - Scheitern mit RECEIPT_NUMBER_INVALID bei abweichendem Format (YYYY-NNNNN).
 * - Scheitern mit RECEIPT_NUMBER_TAKEN bei bereits vergebener Nummer.
 * - Eine vorgegebene Belegnummer setzt den Stand ihres Nummernkreises
 *   mindestens auf ihre Nummer.
 * - In der Erfassungsmaske lässt sich eine Veranstaltung aus allen
 *   Veranstaltungen auswählen und der Betrag frei erfassen; das
 *   Erfassungsdatum ist mit dem heutigen Datum vorbelegt.
 */

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\CashReceipt\AmountInWords;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment\RecordWalkInCashPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment\Request as RecordWalkInRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationSource;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\CashReceipt\RecordWalkInCashPayment as RecordWalkInCashPaymentComponent;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Mail::fake();
    Carbon\Carbon::setTestNow('2026-09-10 15:00:00');
    $this->activity = TestFactory::createActivity(maxParticipants: 1);

    $this->operation = new RecordWalkInCashPayment(
        new RecordPayment(
            app(NextNumber::class),
            new GenerateCashReceiptPdf(new AmountInWords()),
            new SendOutboundMessage(),
        ),
    );
});

afterEach(function (): void {
    Carbon\Carbon::setTestNow();
});

it('creates participant, registration, cash payment and receipt in one go', function (): void {
    $result = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: 'laura@example.com',
        amount: '45.00',
    ));

    expect($result->isSuccess())->toBeTrue();

    $payment = Payment::findById($result->unwrap()->paymentId);
    expect($payment)->not->toBeNull();
    expect($payment->methode)->toBe(PaymentMethod::Cash);
    expect($payment->beleg_art)->toBe('bareinnahmenbeleg');

    $registration = Registration::findById($payment->anmeldung_id);
    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::Confirmed);
    expect($registration->zahlungsart)->toBe(RegistrationPaymentMethod::Cash);
    expect($registration->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);
    expect($registration->herkunft)->toBe(RegistrationSource::Administration);

    $receipt = CashReceipt::findById($result->unwrap()->documentId);
    expect($receipt)->not->toBeNull();
    expect($receipt->nummer)->toBe('2026-00001');
    expect($receipt->empfaenger)->toBe('Laura Laufkundschaft');
    expect($receipt->ausgestellt_am->format('Y-m-d'))->toBe('2026-09-10');
});

it('fails with ACTIVITY_NOT_FOUND for an unknown activity', function (): void {
    $result = $this->operation->execute(new RecordWalkInRequest(
        activityId: '00000000-0000-0000-0000-000000000000',
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: null,
        amount: '45.00',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.not_found');
});

it('fails with PARTICIPANT_NAME_REQUIRED when first or last name is empty', function (): void {
    $leer = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: '   ',
        lastName: 'Laufkundschaft',
        email: null,
        amount: '45.00',
    ));

    expect($leer->error()['code'])->toBe('participant.name_required');

    $ohneNachname = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: '',
        email: null,
        amount: '45.00',
    ));

    expect($ohneNachname->error()['code'])->toBe('participant.name_required');
});

it('fails with PARTICIPANT_EMAIL_INVALID for a malformed email address', function (): void {
    $result = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: 'laura-at-example',
        amount: '45.00',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('participant.email_invalid');
});

it('fails with AMOUNT_INVALID for a missing or non-positive amount', function (): void {
    $null = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: null,
        amount: '0.00',
    ));

    expect($null->error()['code'])->toBe('payment.amount_invalid');

    $negativ = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: null,
        amount: '-5.00',
    ));

    expect($negativ->error()['code'])->toBe('payment.amount_invalid');
});

it('fails with REGISTRATION_ALREADY_EXISTS for an existing non-cancelled registration', function (): void {
    $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: null,
        amount: '45.00',
    ));

    $result = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: null,
        amount: '45.00',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('registration.already_registered');
});

it('reuses an existing participant with exactly matching name without changing their data', function (): void {
    $participant = TestFactory::createParticipant(
        email: 'stamm@example.com',
        vorname: 'Laura',
        nachname: 'Laufkundschaft',
    );

    $result = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Laura',
        lastName: 'Laufkundschaft',
        email: 'andere@example.com',
        amount: '45.00',
    ));

    expect($result->isSuccess())->toBeTrue();

    $payment = Payment::findById($result->unwrap()->paymentId);
    $registration = Registration::findById($payment->anmeldung_id);

    expect($registration->teilnehmer_id)->toBe($participant->id);

    $nachher = Participant::findById($participant->id);
    expect($nachher->email)->toBe('stamm@example.com');
});

it('registers without capacity or waiting-list check', function (): void {
    // maximale Teilnehmerzahl 1 — die Laufkundin ist anwesend und hat gezahlt,
    // die zweite Anmeldung darf trotzdem ohne Warteliste entstehen.
    $erste = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Erste',
        lastName: 'Anwesende',
        email: null,
        amount: '45.00',
    ));
    expect($erste->isSuccess())->toBeTrue();

    $zweite = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Zweite',
        lastName: 'Anwesende',
        email: null,
        amount: '45.00',
    ));

    expect($zweite->isSuccess())->toBeTrue();

    $payment = Payment::findById($zweite->unwrap()->paymentId);
    $registration = Registration::findById($payment->anmeldung_id);
    expect($registration->status)->toBe(RegistrationStatus::Confirmed);
    expect($registration->herkunft)->toBe(RegistrationSource::Administration);
});

it('sends the receipt by email when an email address exists and not otherwise', function (): void {
    $mit = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Mit',
        lastName: 'Adresse',
        email: 'mit@example.com',
        amount: '45.00',
    ));
    expect($mit->isSuccess())->toBeTrue();

    $zweite = TestFactory::createActivity();
    $ohne = (new RecordWalkInCashPayment(
        new RecordPayment(
            app(NextNumber::class),
            new GenerateCashReceiptPdf(new AmountInWords()),
            new SendOutboundMessage(),
        ),
    ))->execute(new RecordWalkInRequest(
        activityId: $zweite->id,
        firstName: 'Ohne',
        lastName: 'Adresse',
        email: null,
        amount: '45.00',
    ));
    expect($ohne->isSuccess())->toBeTrue();

    $mitEmpfaenger = Registration::findById(Payment::findById($mit->unwrap()->paymentId)->anmeldung_id)->teilnehmer_id;
    $ohneEmpfaenger = Registration::findById(Payment::findById($ohne->unwrap()->paymentId)->anmeldung_id)->teilnehmer_id;

    expect(Participant::findById($mitEmpfaenger)->email)->toBe('mit@example.com');
    expect(Participant::findById($ohneEmpfaenger)->email)->toBeNull();

    Mail::assertSent(HtmlAttachmentMail::class, 1);
});

it('uses the given receipt number and issued date in backfill mode', function (): void {
    $result = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Nachgetragen',
        lastName: 'Handschriftlich',
        email: null,
        amount: '45.00',
        receiptNumber: '2026-00007',
        issuedAt: '2026-08-15',
    ));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->documentNumber)->toBe('2026-00007');

    $receipt = CashReceipt::findById($result->unwrap()->documentId);
    expect($receipt->ausgestellt_am->format('Y-m-d'))->toBe('2026-08-15');
});

it('advances the number sequence behind an explicit receipt number', function (): void {
    $nachgepflegt = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Nachgetragen',
        lastName: 'Handschriftlich',
        email: null,
        amount: '45.00',
        receiptNumber: '2026-00007',
        issuedAt: '2026-08-15',
    ));
    expect($nachgepflegt->isSuccess())->toBeTrue();

    $zweite = TestFactory::createActivity();
    $neu = (new RecordWalkInCashPayment(
        new RecordPayment(
            app(NextNumber::class),
            new GenerateCashReceiptPdf(new AmountInWords()),
            new SendOutboundMessage(),
        ),
    ))->execute(new RecordWalkInRequest(
        activityId: $zweite->id,
        firstName: 'Neue',
        lastName: 'Zahlung',
        email: null,
        amount: '45.00',
    ));

    expect($neu->isSuccess())->toBeTrue();
    expect($neu->unwrap()->documentNumber)->toBe('2026-00008');
});

it('fails with RECEIPT_NUMBER_INVALID for a wrong format', function (): void {
    $mitPrefix = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Nachgetragen',
        lastName: 'Handschriftlich',
        email: null,
        amount: '45.00',
        receiptNumber: 'B-2026-00007',
    ));
    expect($mitPrefix->error()['code'])->toBe('receipt.number_invalid');

    $zuKurz = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Nachgetragen',
        lastName: 'Handschriftlich',
        email: null,
        amount: '45.00',
        receiptNumber: '2026-123',
    ));
    expect($zuKurz->error()['code'])->toBe('receipt.number_invalid');
});

it('fails with RECEIPT_NUMBER_TAKEN for an already used number', function (): void {
    $erste = $this->operation->execute(new RecordWalkInRequest(
        activityId: $this->activity->id,
        firstName: 'Erste',
        lastName: 'Zahlung',
        email: null,
        amount: '45.00',
    ));
    expect($erste->unwrap()->documentNumber)->toBe('2026-00001');

    $zweite = TestFactory::createActivity();
    $result = (new RecordWalkInCashPayment(
        new RecordPayment(
            app(NextNumber::class),
            new GenerateCashReceiptPdf(new AmountInWords()),
            new SendOutboundMessage(),
        ),
    ))->execute(new RecordWalkInRequest(
        activityId: $zweite->id,
        firstName: 'Zweite',
        lastName: 'Zahlung',
        email: null,
        amount: '45.00',
        receiptNumber: '2026-00001',
        issuedAt: '2026-09-01',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('receipt.number_taken');
});

it('offers all activities and a free amount with today prefilled in the form', function (): void {
    $component = Livewire::test(RecordWalkInCashPaymentComponent::class);

    expect($component->get('paidAt'))->toBe('2026-09-10T15:00');
    expect($component->get('issuedAt'))->toBe('2026-09-10');

    $activities = $component->get('activities');
    expect($activities)->toHaveCount(1);
    expect($activities[0]->titel)->toBe('Test-Workshop');

    $component->set('activityId', $this->activity->id)
        ->set('firstName', 'Laura')
        ->set('lastName', 'Laufkundschaft')
        ->set('amount', '45.00')
        ->call('save')
        ->assertSet('recorded', true);

    expect(Registration::count())->toBe(1);
});
