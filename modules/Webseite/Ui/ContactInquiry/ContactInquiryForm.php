<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\ContactInquiry;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry\RecordContactInquiry;
use Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry\Request as RecordRequest;
use Yoga\Modules\Webseite\Application\Page\GetPageContent;

#[Layout('webseite::layouts.app')]
final class ContactInquiryForm extends Component
{
    /**
     * CMS-Inhalt der Kontaktseite (die dunkle Kontaktkarte).
     */
    public ?object $page = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $topic = '';

    public string $message = '';

    /** Verstecktes Spamschutz-Feld (Honeypot) — Besucher/innen füllen es nicht. */
    public string $website = '';

    public string $error = '';

    public bool $submitted = false;

    public function mount(GetPageContent $query): void
    {
        $this->page = $query->execute('kontakt');
    }

    public function submit(RecordContactInquiry $operation): void
    {
        $this->error = '';
        $this->submitted = false;

        $result = $operation->execute(new RecordRequest(
            name: $this->name,
            email: $this->email,
            phone: $this->phone === '' ? null : $this->phone,
            topic: $this->topic === '' ? null : $this->topic,
            message: $this->message,
            honeypot: $this->website,
            ipAddress: (string) request()->ip(),
        ));

        if ($result->isFailure()) {
            $this->error = match ($result->error()['code'] ?? '') {
                'contact_inquiry.invalid_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.',
                'contact_inquiry.rate_limited' => 'Es wurden zu viele Anfragen gesendet. Bitte versuchen Sie es später erneut.',
                default => 'Bitte füllen Sie alle Pflichtfelder aus.',
            };

            return;
        }

        $this->submitted = true;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('webseite::ContactInquiry.contact-inquiry');
    }
}
