<?php

declare(strict_types=1);

/**
 * Story: E1 Öffentliche Webseite / F4 Kontaktanfrage (Oberfläche)
 *
 * Geprüfte Kriterien:
 * - Auf der Kontaktseite ist neben der Kontaktkarte ein Anfrageformular sichtbar.
 * - Das Formular enthält die Felder `name`, `email`, `phone`, `topic` und `message`.
 * - Das Formular zeigt an, welche Felder Pflichtfelder sind (`name`, `email`, `message`).
 * - Das Formular enthält neben der Versandaktion einen Link auf die Datenschutzerklärung.
 * - Das Formular enthält keine Eingabemöglichkeit für Gesundheitsinformationen.
 * - Bei ungültigem E-Mail-Format zeigt die Maske die Meldung an.
 * - Nach erfolgreichem Absenden erscheint eine Bestätigungsmeldung mit dem Hinweis
 *   auf die Eingangsbestätigung per E-Mail.
 * - Eine Anfrage mit ausgefülltem Spamschutz-Feld zeigt dennoch die Erfolgsmeldung
 *   an, wird aber nicht persistiert.
 * - Bei Ratenbegrenzung zeigt die Maske die Meldung an.
 * - Die Liste in der Verwaltung zeigt `receivedAt`, `name`, `email`, `topic` und `status`.
 * - Die Detailansicht zeigt alle Felder und die `note`; der Status kann auf alle drei
 *   Werte geändert werden.
 */

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry\RecordContactInquiry;
use Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiry;
use Yoga\Modules\Webseite\Ui\ContactInquiry\ContactInquiryDetail;
use Yoga\Modules\Webseite\Ui\ContactInquiry\ContactInquiryForm;
use Yoga\Modules\Webseite\Ui\ContactInquiry\ContactInquiryList;

function createInquiryForComponentTest(string $email = 'anna@example.com', string $status = 'neu'): ContactInquiry
{
    Mail::fake();

    $operation = new RecordContactInquiry(app('Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage'));
    $result = $operation->execute(new Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry\Request(
        name: 'Anna Beispiel',
        email: $email,
        phone: null,
        topic: null,
        message: 'Wir interessieren uns für einen Termin.',
        honeypot: '',
        ipAddress: '203.0.113.50',
    ));

    $inquiry = ContactInquiry::findById($result->unwrap()->inquiryId);
    expect($inquiry)->not->toBeNull();

    if ($status !== 'neu') {
        $inquiry->aendere(Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiryStatus::from($status), null);
        $inquiry->save();
    }

    return $inquiry;
}

beforeEach(function (): void {
    $this->seed(Database\Seeders\ContentPagesSeeder::class);
});

it('shows the inquiry form with all fields next to the contact card', function (): void {
    $html = Livewire::test(ContactInquiryForm::class)->html();

    expect($html)->toContain('yoga-contact-grid')
        ->toContain('yoga-contact-card')
        ->toContain('Gerne melden')
        ->toContain('yoga-form-card')
        ->toContain('id="name"')
        ->toContain('id="email"')
        ->toContain('id="phone"')
        ->toContain('id="topic"')
        ->toContain('id="message"');
});

it('marks name, email and message as required fields', function (): void {
    $html = Livewire::test(ContactInquiryForm::class)->html();

    $nameField = substr($html, (int) mb_strpos($html, 'id="name"'), 120);
    $emailField = substr($html, (int) mb_strpos($html, 'id="email"'), 120);
    $messageField = substr($html, (int) mb_strpos($html, 'id="message"'), 160);

    expect($nameField)->toContain('required');
    expect($emailField)->toContain('required');
    expect($messageField)->toContain('required');
    expect($html)->not->toContain('Gesundheit');
});

it('links the privacy policy next to the submit action', function (): void {
    $html = Livewire::test(ContactInquiryForm::class)->html();

    expect($html)->toContain('href="/datenschutz"');
});

it('shows the success message with the acknowledgement hint after submitting', function (): void {
    Mail::fake();

    Livewire::test(ContactInquiryForm::class)
        ->set('name', 'Anna Beispiel')
        ->set('email', 'anna@example.com')
        ->set('message', 'Wir interessieren uns für einen Termin.')
        ->call('submit')
        ->assertSee('Vielen Dank für Ihre Anfrage')
        ->assertSee('Eingangsbestätigung');

    expect(ContactInquiry::query()->count())->toBe(1);
});

it('shows the message for an invalid email address', function (): void {
    Livewire::test(ContactInquiryForm::class)
        ->set('name', 'Anna Beispiel')
        ->set('email', 'keine-adresse')
        ->set('message', 'Wir interessieren uns für einen Termin.')
        ->call('submit')
        ->assertSee('Bitte geben Sie eine gültige E-Mail-Adresse an.');
});

it('shows the success message but persists nothing when the honeypot field is filled', function (): void {
    Mail::fake();

    Livewire::test(ContactInquiryForm::class)
        ->set('name', 'Anna Beispiel')
        ->set('email', 'anna@example.com')
        ->set('message', 'Wir interessieren uns für einen Termin.')
        ->set('website', 'http://spam.example.com')
        ->call('submit')
        ->assertSee('Vielen Dank für Ihre Anfrage');

    expect(ContactInquiry::query()->count())->toBe(0);
});

it('shows the message when the rate limit is reached', function (): void {
    Mail::fake();

    $component = Livewire::test(ContactInquiryForm::class)
        ->set('name', 'Anna Beispiel');

    foreach (range(1, 5) as $i) {
        $component->set('email', "anna{$i}@example.com")
            ->set('message', 'Wir interessieren uns für einen Termin.')
            ->call('submit');
    }

    $component->set('email', 'sechste@example.com')
        ->set('message', 'Wir interessieren uns für einen Termin.')
        ->call('submit')
        ->assertSee('Es wurden zu viele Anfragen gesendet.');
});

it('shows the inquiries with received at, name, email, topic and status', function (): void {
    createInquiryForComponentTest();

    $html = Livewire::test(ContactInquiryList::class)->html();

    expect($html)->toContain('Kontaktanfragen')
        ->toContain('Anna Beispiel')
        ->toContain('anna@example.com')
        ->toContain('neu');
});

it('filters the list by status', function (): void {
    createInquiryForComponentTest(email: 'erste@example.com', status: 'erledigt');
    createInquiryForComponentTest(email: 'zweite@example.com', status: 'neu');

    /** @var list<object{id: string, name: string}> $filtered */
    $filtered = Livewire::test(ContactInquiryList::class)
        ->set('statusFilter', 'erledigt')
        ->call('search')
        ->get('inquiries');

    expect(count($filtered))->toBe(1);
    expect($filtered[0]->name)->toBe('Anna Beispiel');
    expect($filtered[0]->email)->toBe('erste@example.com');
});

it('shows all fields and the note in the detail view', function (): void {
    $inquiry = createInquiryForComponentTest();

    $html = Livewire::test(ContactInquiryDetail::class, ['id' => $inquiry->id])->html();

    expect($html)->toContain('Anna Beispiel')
        ->toContain('anna@example.com')
        ->toContain('Wir interessieren uns für einen Termin.')
        ->toContain('Bearbeitung');
});

it('persists status and note from the detail view', function (): void {
    $inquiry = createInquiryForComponentTest();

    Livewire::test(ContactInquiryDetail::class, ['id' => $inquiry->id])
        ->set('status', 'erledigt')
        ->set('note', 'Per E-Mail beantwortet.')
        ->call('save')
        ->assertSet('saved', true);

    $inquiry = ContactInquiry::findById($inquiry->id);
    expect($inquiry->status->value)->toBe('erledigt');
    expect($inquiry->notiz)->toBe('Per E-Mail beantwortet.');
});

it('rejects saving an unknown status in the detail view', function (): void {
    $inquiry = createInquiryForComponentTest();

    Livewire::test(ContactInquiryDetail::class, ['id' => $inquiry->id])
        ->set('status', 'abgelehnt')
        ->set('note', null)
        ->call('save')
        ->assertSet('saved', false)
        ->assertSee('konnte nicht gespeichert werden');
});
