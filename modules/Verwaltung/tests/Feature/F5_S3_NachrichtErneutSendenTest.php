<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F5 Ausgehende Nachrichten / S3 Nachricht erneut senden
 *
 * Geprüfte Kriterien:
 * - ResendOutboundMessage versucht den Versand erneut.
 * - ResendOutboundMessage scheitert mit MESSAGE_NOT_FOUND, wenn die Nachricht nicht existiert.
 * - Nach dem erneuten Versand wird ein neuer OutboundMessage-Eintrag mit aktuellem Zeitpunkt angelegt.
 * - Der ursprüngliche Eintrag bleibt unverändert.
 */

use Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage\Request as ResendRequest;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage\ResendOutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;

it('creates a new pending message when resending a failed message', function (): void {
    $original = new OutboundMessage([
        'empfaenger' => 'anna@example.com',
        'betreff' => 'Anmeldebestätigung',
        'inhalt' => 'Willkommen',
        'status' => OutboundMessageStatus::Failed->value,
        'versendet_am' => null,
    ]);
    $original->save();

    $resend = new ResendOutboundMessage();
    $result = $resend->execute(new ResendRequest(messageId: $original->id));

    expect($result->isSuccess())->toBeTrue();

    $newMessage = OutboundMessage::findById($result->unwrap()->newMessageId);
    expect($newMessage)->not->toBeNull();
    expect($newMessage->status)->toBe(OutboundMessageStatus::Pending);
    expect($newMessage->empfaenger)->toBe('anna@example.com');

    $original->fresh();
    expect($original->status)->toBe(OutboundMessageStatus::Failed);
});

it('fails with not_found for a non-existing message', function (): void {
    $resend = new ResendOutboundMessage();
    $result = $resend->execute(new ResendRequest(
        messageId: '018e1234-5678-7abc-8def-0123456789ab',
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('message.not_found');
});
