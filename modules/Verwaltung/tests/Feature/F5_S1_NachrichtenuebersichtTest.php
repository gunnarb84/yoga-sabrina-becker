<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F5 Ausgehende Nachrichten / S1 Nachrichtenübersicht anzeigen
 *
 * Geprüfte Kriterien:
 * - Die Liste zeigt recipient, subject, status und sentAt.
 * - Die Liste lässt sich nach status und recipient filtern.
 */

use Yoga\Modules\Verwaltung\Application\OutboundMessage\OutboundMessages\OutboundMessagesQuery;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;

beforeEach(function (): void {
    new OutboundMessage([
        'empfaenger' => 'anna@example.com',
        'betreff' => 'Anmeldebestätigung',
        'inhalt' => 'Willkommen',
        'status' => OutboundMessageStatus::Sent->value,
        'versendet_am' => now(),
    ])->save();

    new OutboundMessage([
        'empfaenger' => 'bernd@example.com',
        'betreff' => 'Wartelisteninfo',
        'inhalt' => 'Info',
        'status' => OutboundMessageStatus::Failed->value,
        'versendet_am' => null,
    ])->save();

    new OutboundMessage([
        'empfaenger' => 'clara@example.com',
        'betreff' => 'Rechnung',
        'inhalt' => 'Bitte überweisen',
        'status' => OutboundMessageStatus::Pending->value,
        'versendet_am' => null,
    ])->save();
});

it('returns all outbound messages ordered by creation time', function (): void {
    $query = new OutboundMessagesQuery();
    $messages = $query->execute();

    expect($messages)->toHaveCount(3);
    expect($messages[0]->status)->toBe(OutboundMessageStatus::Pending->value);
    expect($messages[0]->empfaenger)->toBe('clara@example.com');
});

it('filters messages by status', function (): void {
    $query = new OutboundMessagesQuery();
    $messages = $query->execute(statusFilter: OutboundMessageStatus::Sent->value);

    expect($messages)->toHaveCount(1);
    expect($messages[0]->empfaenger)->toBe('anna@example.com');
});

it('filters messages by recipient', function (): void {
    $query = new OutboundMessagesQuery();
    $messages = $query->execute(recipientFilter: 'bernd');

    expect($messages)->toHaveCount(1);
    expect($messages[0]->empfaenger)->toBe('bernd@example.com');
});
