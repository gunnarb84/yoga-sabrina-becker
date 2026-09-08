<?php

declare(strict_types=1);

/**
 * Story: E1 Öffentliche Webseite / F4 Kontaktanfrage
 *
 * Geprüfte Kriterien:
 * - Die gespeicherte Anfrage trägt den Status „Neu".
 * - Der Vorgang prüft das Format von `email`; bei ungültigem Format scheitert er mit
 *   dem Fehlercode `contact_inquiry.invalid_email`.
 * - Der Vorgang scheitert, wenn ein Pflichtfeld fehlt, mit dem Fehlercode
 *   `contact_inquiry.invalid_input`.
 * - Eine Anfrage mit ausgefülltem versteckten Spamschutz-Feld wird nicht persistiert
 *   und nicht per E-Mail versendet; der Vorgang antwortet dennoch mit Erfolg.
 * - Der Vorgang erlaubt höchstens 5 Anfragen innerhalb von 10 Minuten je IP-Adresse;
 *   darüber hinaus scheitert er mit dem Fehlercode `contact_inquiry.rate_limited`.
 * - Nach erfolgreichem Absenden wird eine Eingangsbestätigung an die angegebene
 *   `email` versendet; sie enthält keine Gesundheitsinformationen.
 * - Nach erfolgreichem Absenden wird eine Benachrichtigung an die hinterlegte
 *   Empfängeradresse der Inhaberin versendet, die name, email, phone, topic und
 *   message enthält.
 * - Beide E-Mails werden als OutboundMessage mit Status SENT oder FAILED protokolliert.
 * - Die Notiz wird persistiert.
 * - Der Status kann auf jeden der drei Werte geändert werden; ein unbekannter Status
 *   scheitert mit dem Fehlercode `contact_inquiry.invalid_status`.
 * - Eine unbekannte Anfrage scheitert mit dem Fehlercode `contact_inquiry.not_found`.
 * - Die Liste zeigt die Anfragen absteigend nach Empfangszeitpunkt und lässt sich
 *   nach Status filtern.
 */

use Illuminate\Support\Facades\Mail;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Webseite\Application\ContactInquiry\ListContactInquiries\ListContactInquiries;
use Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry\RecordContactInquiry;
use Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry\Request as RecordRequest;
use Yoga\Modules\Webseite\Application\ContactInquiry\UpdateContactInquiry\Request as UpdateRequest;
use Yoga\Modules\Webseite\Application\ContactInquiry\UpdateContactInquiry\UpdateContactInquiry;
use Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiry;

function submitInquiry(array $overrides = []): \Yoga\Platform\Shared\Application\Result
{
    $operation = new RecordContactInquiry(new SendOutboundMessage());

    return $operation->execute(new RecordRequest(
        name: $overrides['name'] ?? 'Anna Beispiel',
        email: $overrides['email'] ?? 'anna@example.com',
        phone: $overrides['phone'] ?? '0150 12345678',
        topic: $overrides['topic'] ?? 'Yoga auf der Burg',
        message: $overrides['message'] ?? 'Wir sind eine Gruppe von acht Personen und interessieren uns für einen Termin im Herbst.',
        honeypot: $overrides['honeypot'] ?? '',
        ipAddress: $overrides['ipAddress'] ?? '203.0.113.10',
    ));
}

it('persists an inquiry with status neu', function (): void {
    Mail::fake();

    $result = submitInquiry();

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->inquiryId)->not->toBeNull();

    $inquiry = ContactInquiry::findById($result->unwrap()->inquiryId);
    expect($inquiry)->not->toBeNull();
    expect($inquiry->name)->toBe('Anna Beispiel');
    expect($inquiry->email)->toBe('anna@example.com');
    expect($inquiry->telefon)->toBe('0150 12345678');
    expect($inquiry->anlass)->toBe('Yoga auf der Burg');
    expect($inquiry->status->value)->toBe('neu');
});

it('rejects an inquiry with an invalid email address', function (): void {
    $result = submitInquiry(['email' => 'keine-adresse']);

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('contact_inquiry.invalid_email');
});

it('rejects an inquiry with a missing required field', function (): void {
    $result = submitInquiry(['message' => '']);

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('contact_inquiry.invalid_input');
});

it('discards an inquiry with a filled honeypot field but reports success', function (): void {
    Mail::fake();

    $result = submitInquiry(['honeypot' => 'http://spam.example.com']);

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->inquiryId)->toBeNull();

    expect(ContactInquiry::query()->count())->toBe(0);
    Mail::assertNothingSent();
});

it('allows at most five inquiries per ip address within ten minutes', function (): void {
    Mail::fake();

    foreach (range(1, 5) as $i) {
        $result = submitInquiry(['email' => "anna{$i}@example.com"]);

        expect($result->isSuccess())->toBeTrue();
    }

    $sixth = submitInquiry(['email' => 'sechste@example.com']);

    expect($sixth->isFailure())->toBeTrue();
    expect($sixth->error()['code'])->toBe('contact_inquiry.rate_limited');
    expect(ContactInquiry::query()->count())->toBe(5);
});

it('sends an acknowledgement email without health information', function (): void {
    Mail::fake();

    $result = submitInquiry();

    expect($result->isSuccess())->toBeTrue();

    $messages = OutboundMessage::query()->where('empfaenger', 'anna@example.com')->get();
    expect($messages)->toHaveCount(1);

    $message = $messages->first();
    expect($message->betreff)->toBe('Eingangsbestätigung Ihrer Anfrage');
    expect($message->inhalt)->toContain('Anna Beispiel');
    expect($message->inhalt)->not->toContain('Gesundheit');
    expect($message->inhalt)->not->toContain('Krankheit');
});

it('sends a notification email to the owner containing all inquiry fields', function (): void {
    Mail::fake();

    $result = submitInquiry();

    expect($result->isSuccess())->toBeTrue();

    $expectedRecipient = (string) config('mail.contact_inquiry_recipient');
    $messages = OutboundMessage::query()->where('empfaenger', $expectedRecipient)->get();
    expect($messages)->toHaveCount(1);

    $message = $messages->first();
    expect($message->betreff)->toContain('Neue Kontaktanfrage');
    expect($message->inhalt)->toContain('Anna Beispiel');
    expect($message->inhalt)->toContain('anna@example.com');
    expect($message->inhalt)->toContain('0150 12345678');
    expect($message->inhalt)->toContain('Yoga auf der Burg');
    expect($message->inhalt)->toContain('Gruppe von acht Personen');
});

it('logs both emails as outbound messages with an end status', function (): void {
    Mail::fake();

    $result = submitInquiry();

    expect($result->isSuccess())->toBeTrue();

    $messages = OutboundMessage::query()->where('empfaenger', '!=', '')->get();
    expect($messages)->toHaveCount(2);

    foreach ($messages as $message) {
        expect($message->status->value)->toBe('versandt');
    }
});

it('persists the note of an inquiry', function (): void {
    Mail::fake();

    $result = submitInquiry();
    $inquiryId = $result->unwrap()->inquiryId;

    $operation = new UpdateContactInquiry();
    $update = $operation->execute(new UpdateRequest(
        inquiryId: $inquiryId,
        status: 'in_bearbeitung',
        note: 'Telefonisch zurückgerufen am 10.09.2026.',
    ));

    expect($update->isSuccess())->toBeTrue();

    $inquiry = ContactInquiry::findById($inquiryId);
    expect($inquiry->status->value)->toBe('in_bearbeitung');
    expect($inquiry->notiz)->toBe('Telefonisch zurückgerufen am 10.09.2026.');
});

it('changes the status to each of the three values', function (): void {
    Mail::fake();

    $inquiryId = submitInquiry()->unwrap()->inquiryId;
    $operation = new UpdateContactInquiry();

    foreach (['in_bearbeitung', 'erledigt', 'neu'] as $status) {
        $update = $operation->execute(new UpdateRequest(
            inquiryId: $inquiryId,
            status: $status,
            note: null,
        ));

        expect($update->isSuccess())->toBeTrue();

        $inquiry = ContactInquiry::findById($inquiryId);
        expect($inquiry->status->value)->toBe($status);
    }
});

it('rejects an unknown status', function (): void {
    Mail::fake();

    $inquiryId = submitInquiry()->unwrap()->inquiryId;
    $operation = new UpdateContactInquiry();
    $update = $operation->execute(new UpdateRequest(
        inquiryId: $inquiryId,
        status: 'abgelehnt',
        note: null,
    ));

    expect($update->isFailure())->toBeTrue();
    expect($update->error()['code'])->toBe('contact_inquiry.invalid_status');
});

it('rejects an update of an unknown inquiry', function (): void {
    $operation = new UpdateContactInquiry();
    $update = $operation->execute(new UpdateRequest(
        inquiryId: '0197f8d3-0000-7000-8000-000000000000',
        status: 'in_bearbeitung',
        note: null,
    ));

    expect($update->isFailure())->toBeTrue();
    expect($update->error()['code'])->toBe('contact_inquiry.not_found');
});

it('lists inquiries newest first and filters by status', function (): void {
    Mail::fake();

    $firstId = submitInquiry(['email' => 'erste@example.com'])->unwrap()->inquiryId;
    $secondId = submitInquiry(['email' => 'zweite@example.com'])->unwrap()->inquiryId;

    $operation = new UpdateContactInquiry();
    $operation->execute(new UpdateRequest(
        inquiryId: $firstId,
        status: 'erledigt',
        note: null,
    ));

    $list = new ListContactInquiries();

    $all = $list->execute();
    expect(count($all))->toBe(2);
    expect($all[0]->id)->toBe($secondId);
    expect($all[1]->id)->toBe($firstId);

    $done = $list->execute('erledigt');
    expect(count($done))->toBe(1);
    expect($done[0]->id)->toBe($firstId);

    $none = $list->execute('unbekannt');
    expect($none)->toBe([]);
});
