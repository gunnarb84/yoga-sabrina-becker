<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten / S2 Rechnungen verwalten
 *
 * Geprüfte Kriterien:
 * - Eine Rechnung kann als PDF heruntergeladen werden.
 * - Eine Rechnung kann erneut per E-Mail an die Teilnehmer-E-Mail versendet werden.
 */

use Illuminate\Support\Facades\Mail;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\GenerateInvoicePdf;
use Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\Invoice\SendInvoice\Request as SendInvoiceRequest;
use Yoga\Modules\Verwaltung\Application\Invoice\SendInvoice\SendInvoice;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Verwaltung\Ui\Mail\HtmlAttachmentMail;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity();
    $this->participant = TestFactory::createParticipant(email: 'rechnung@example.com');

    $register = new RegisterParticipant(app(NextNumber::class));
    $this->registration = $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $this->participant->id,
        paymentMethod: 'ueberweisung',
    ));

    expect($this->registration->unwrap()->invoiceId)->toBeUuidString();
    $this->invoiceId = $this->registration->unwrap()->invoiceId;
});

it('generates a downloadable PDF for an invoice', function (): void {
    $generator = new GenerateInvoicePdf();
    $result = $generator->execute(new GeneratePdfRequest($this->invoiceId));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->content)->not->toBe('');
    expect($result->unwrap()->filename)->toStartWith('Rechnung-R-');
});

it('resends an invoice by email with a PDF attachment', function (): void {
    Mail::fake();

    $send = new SendInvoice(new GenerateInvoicePdf());
    $result = $send->execute(new SendInvoiceRequest(
        invoiceId: $this->invoiceId,
        subject: 'Rechnung',
        body: 'Im Anhang finden Sie die Rechnung.',
    ));

    expect($result->isSuccess())->toBeTrue();

    $message = OutboundMessage::findById($result->unwrap()->outboundMessageId);
    expect($message)->not->toBeNull();
    expect($message->empfaenger)->toBe('rechnung@example.com');
    expect($message->betreff)->toBe('Rechnung');
    expect($message->status)->toBe(OutboundMessageStatus::Sent);

    Mail::assertSent(HtmlAttachmentMail::class, function (HtmlAttachmentMail $mail): bool {
        return $mail->hasTo('rechnung@example.com')
            && $mail->subjectText === 'Rechnung'
            && $mail->attachment !== null
            && $mail->attachment['mime'] === 'application/pdf';
    });
});
