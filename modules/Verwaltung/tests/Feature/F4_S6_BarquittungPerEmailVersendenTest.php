<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S6 Barquittung per E-Mail versenden
 *
 * Geprüfte Kriterien:
 * - Bei der Erfassung einer Barzahlung wird die Barquittung als PDF automatisch
 *   per E-Mail an die email der Teilnehmerin/des Teilnehmers versendet.
 * - Die E-Mail enthält das Beleg-PDF als Anhang.
 * - Der Versand wird als OutboundMessage mit dem Betreff und dem Status Versandt
 *   protokolliert.
 * - Scheitert der E-Mail-Versand, wird die Zahlung trotzdem erfasst und die
 *   ausgehende Nachricht erhält den Status Fehlgeschlagen.
 * - Eine fehlgeschlagene Nachricht kann über ResendOutboundMessage erneut
 *   versendet werden (siehe F5).
 * - Kostenlose Anmeldungen erhalten keine Barquittung und keine E-Mail.
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Yoga\Modules\Verwaltung\Application\CashReceipt\AmountInWords;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage\Request as ResendRequest;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage\ResendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Registration\ListOpenCashRegistrations\ListOpenCashRegistrationsQuery;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Mail::fake();
    Carbon::setTestNow('2026-09-02 12:00:00');
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function barzahlungVorgang(): RecordPayment
{
    return new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(new AmountInWords()), new SendOutboundMessage());
}

function legeBarAnmeldungAn(object $activity, string $email): string
{
    $participant = TestFactory::createParticipant(email: $email);
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    return $registration->unwrap()->registrationId;
}

it('sends the receipt as PDF by email to the participant when a cash payment is recorded', function (): void {
    $participant = TestFactory::createParticipant(email: 'quittung@example.com');
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    $record = barzahlungVorgang();
    $result = $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: 'bar',
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    expect($result->isSuccess())->toBeTrue();

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail) use ($participant): bool {
        return $mail->hasTo($participant->email)
            && str_contains($mail->subjectText, 'Barquittung')
            && $mail->attachment !== null
            && str_starts_with($mail->attachment['filename'], 'Barquittung-2026-');
    });
});

it('logs the receipt email as an outbound message with status sent', function (): void {
    $registrationId = legeBarAnmeldungAn($this->activity, 'protokoll@example.com');

    $record = barzahlungVorgang();
    $record->execute(new RecordPaymentRequest(
        registrationId: $registrationId,
        method: 'bar',
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    $message = OutboundMessage::query()->orderByDesc('angelegt_am')->first();

    expect($message)->not->toBeNull();
    expect($message->betreff)->toContain('Barquittung 2026-00001');
    expect($message->status)->toBe(OutboundMessageStatus::Sent);
    expect($message->empfaenger)->toBe('protokoll@example.com');
});

it('keeps the payment recorded when the email delivery fails', function (): void {
    Mail::shouldReceive('to')->andReturnSelf();
    Mail::shouldReceive('send')->andThrow(new RuntimeException('SMTP nicht erreichbar'));

    $registrationId = legeBarAnmeldungAn($this->activity, 'fehler@example.com');

    $record = barzahlungVorgang();
    $result = $record->execute(new RecordPaymentRequest(
        registrationId: $registrationId,
        method: 'bar',
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    expect($result->isSuccess())->toBeTrue();

    $registration = Registration::findById($registrationId);
    expect($registration->zahlungsstatus)->toBe(RegistrationPaymentStatus::Paid);

    $message = OutboundMessage::query()->orderByDesc('angelegt_am')->first();
    expect($message->status)->toBe(OutboundMessageStatus::Failed);
});

it('resubmits a failed receipt email via ResendOutboundMessage', function (): void {
    Mail::shouldReceive('to')->andReturnSelf();
    Mail::shouldReceive('send')->andThrow(new RuntimeException('SMTP nicht erreichbar'));

    $registrationId = legeBarAnmeldungAn($this->activity, 'wiederholung@example.com');
    $record = barzahlungVorgang();
    $record->execute(new RecordPaymentRequest(
        registrationId: $registrationId,
        method: 'bar',
        amount: '45.00',
        paidAt: '2026-09-02 12:00:00',
        recipient: 'Max Mustermann',
    ));

    $failed = OutboundMessage::query()->orderByDesc('angelegt_am')->first();
    expect($failed->status)->toBe(OutboundMessageStatus::Failed);

    $resend = new ResendOutboundMessage();
    $result = $resend->execute(new ResendRequest(messageId: $failed->id));

    expect($result->isSuccess())->toBeTrue();

    $resendResult = $result->unwrap();
    $resent = OutboundMessage::findById($resendResult->newMessageId);
    expect($resent->status)->toBe(OutboundMessageStatus::Pending);
    expect($resent->empfaenger)->toBe('wiederholung@example.com');
});

it('does not issue a receipt or email for free registrations', function (): void {
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

    $query = new ListOpenCashRegistrationsQuery();
    $rows = $query->execute($freeActivity->id);

    expect($rows)->toHaveCount(0);
    expect(CashReceipt::count())->toBe(0);
    expect(Mail::sent(HtmlAttachmentMail::class)->count())->toBe(0);
});
