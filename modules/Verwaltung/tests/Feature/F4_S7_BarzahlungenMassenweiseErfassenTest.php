<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S7 Barzahlungen massenweise erfassen
 *
 * Geprüfte Kriterien:
 * - Der Vorgang RecordCashPaymentBatch nimmt eine Veranstaltung und je Anmeldung
 *   einen „bezahlt"-Vermerk mit Zahlungszeitpunkt entgegen.
 * - Die Abfrage ListOpenCashRegistrations liefert alle Anmeldungen einer
 *   Veranstaltung mit Zahlungsart Bar, Status Bestätigt und ohne erfasste Zahlung.
 * - Für jede angehakte Anmeldung erzeugt RecordCashPaymentBatch eine Payment mit
 *   der Methode CASH und einen CashReceipt mit der nächsten freien lückenlosen
 *   Nummer im Format B-YYYY-NNNNN.
 * - Der Zahlungsstatus jeder erfassten Anmeldung wird auf „bezahlt" gesetzt.
 * - Anmeldungen mit Zahlungsart Überweisung oder Kostenlos erscheinen nicht in
 *   der Massenerfassung.
 * - Der Vorgang scheitert für eine einzelne Anmeldung mit dem Fehlercode
 *   ALREADY_PAID, wenn zwischen Laden und Speichern bereits eine Zahlung erfasst
 *   wurde; die übrigen Anmeldungen werden trotzdem erfasst.
 * - Der Vorgang scheitert mit dem Fehlercode ACTIVITY_NOT_FOUND, wenn die
 *   Veranstaltung nicht existiert.
 * - Für jede erfasste Zahlung wird die Barquittung per E-Mail versandt (siehe S6).
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch\Item as BatchItem;
use Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch\RecordCashPaymentBatch;
use Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch\Request as BatchRequest;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Registration\ListOpenCashRegistrations\ListOpenCashRegistrationsQuery;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Mail::fake();
    Carbon::setTestNow('2026-09-02 12:00:00');
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function massenVorgang(): RecordCashPaymentBatch
{
    return new RecordCashPaymentBatch(new RecordPayment(
        app(NextNumber::class),
        new GenerateCashReceiptPdf(),
        new SendOutboundMessage(),
    ));
}

/**
 * Legt eine Anmeldung mit der angegebenen Zahlungsart an.
 */
function legeAnmeldungAn(object $activity, string $email, string $zahlungsart): Registration
{
    $participant = TestFactory::createParticipant(email: $email);
    $register = new RegisterParticipant(app(NextNumber::class));
    $result = $register->execute(new RegisterRequest(
        activityId: $activity->id,
        participantId: $participant->id,
        paymentMethod: $zahlungsart,
    ));

    expect($result->isSuccess())->toBeTrue();

    $registration = Registration::findById($result->unwrap()->registrationId);
    expect($registration)->not->toBeNull();

    return $registration;
}

it('lists only open cash registrations of an activity', function (): void {
    $activity = TestFactory::createActivity(maxParticipants: 1);

    $offen = legeAnmeldungAn($activity, 'offen@example.com', 'bar');
    legeAnmeldungAn($activity, 'warteliste@example.com', 'bar'); // voll → Warteliste

    $query = new ListOpenCashRegistrationsQuery();
    $rows = $query->execute($activity->id);

    expect($rows)->toHaveCount(1);
    expect($rows[0]->registrationId)->toBe($offen->id);
});

it('excludes transfer, free and paid registrations from the batch list', function (): void {
    $activity = TestFactory::createActivity(maxParticipants: 4);

    $bezahlt = legeAnmeldungAn($activity, 'bezahlt@example.com', 'bar');

    legeAnmeldungAn($activity, 'ueberweisung@example.com', 'ueberweisung');
    legeAnmeldungAn($activity, 'kostenlos@example.com', 'kostenlos');

    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());
    $record->execute(new RecordPaymentRequest(
        registrationId: $bezahlt->id,
        method: 'bar',
        amount: '45.00',
        paidAt: '2026-09-02 11:00:00',
        recipient: 'Max Bezahlt',
    ));

    $query = new ListOpenCashRegistrationsQuery();
    $rows = $query->execute($activity->id);

    expect($rows)->toHaveCount(0);
});

it('records payments and gapless receipts for every selected registration', function (): void {
    $activity = TestFactory::createActivity(maxParticipants: 4);

    $erste = legeAnmeldungAn($activity, 'erste@example.com', 'bar');
    $zweite = legeAnmeldungAn($activity, 'zweite@example.com', 'bar');

    $result = massenVorgang()->execute(new BatchRequest(
        $activity->id,
        [
            new BatchItem($erste->id, '2026-09-02 12:00:00'),
            new BatchItem($zweite->id, '2026-09-02 12:00:00'),
        ],
    ));

    expect($result->isSuccess())->toBeTrue();

    $response = $result->unwrap();
    expect($response->recordedCount)->toBe(2);
    expect($response->skippedCount)->toBe(0);
    expect($response->outcomes[0]->receiptNumber)->toBe('B-2026-00001');
    expect($response->outcomes[1]->receiptNumber)->toBe('B-2026-00002');
    expect(CashReceipt::count())->toBe(2);

    $ersteNeu = Registration::findById($erste->id);
    $zweiteNeu = Registration::findById($zweite->id);
    expect($ersteNeu->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);
    expect($zweiteNeu->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);
});

it('fails with ACTIVITY_NOT_FOUND for an unknown activity', function (): void {
    $result = massenVorgang()->execute(new BatchRequest(
        '00000000-0000-0000-0000-000000000000',
        [],
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.not_found');
});

it('skips a registration that was paid between load and save and records the rest', function (): void {
    $activity = TestFactory::createActivity(maxParticipants: 4);

    $erste = legeAnmeldungAn($activity, 'erste@example.com', 'bar');
    $zweite = legeAnmeldungAn($activity, 'zweite@example.com', 'bar');

    // Zwischen Laden und Speichern wurde die erste Anmeldung bereits erfasst.
    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());
    $record->execute(new RecordPaymentRequest(
        registrationId: $erste->id,
        method: 'bar',
        amount: '45.00',
        paidAt: '2026-09-02 11:00:00',
        recipient: 'Max Erste',
    ));

    $result = massenVorgang()->execute(new BatchRequest(
        $activity->id,
        [
            new BatchItem($erste->id, '2026-09-02 12:00:00'),
            new BatchItem($zweite->id, '2026-09-02 12:00:00'),
        ],
    ));

    expect($result->isSuccess())->toBeTrue();

    $response = $result->unwrap();
    expect($response->recordedCount)->toBe(1);
    expect($response->skippedCount)->toBe(1);
    expect($response->outcomes[0]->outcome)->toBe('uebersprungen');
    expect($response->outcomes[0]->errorCode)->toBe('registration.already_paid');
    expect($response->outcomes[1]->outcome)->toBe('erfasst');

    expect(CashReceipt::count())->toBe(2); // 1 vorab + 1 aus dem Massenlauf
});

it('sends a receipt email for every recorded payment', function (): void {
    $activity = TestFactory::createActivity(maxParticipants: 4);

    $erste = legeAnmeldungAn($activity, 'erste@example.com', 'bar');
    $zweite = legeAnmeldungAn($activity, 'zweite@example.com', 'bar');

    massenVorgang()->execute(new BatchRequest(
        $activity->id,
        [
            new BatchItem($erste->id, null),
            new BatchItem($zweite->id, null),
        ],
    ));

    Mail::assertSent(HtmlAttachmentMail::class, 2);
});
