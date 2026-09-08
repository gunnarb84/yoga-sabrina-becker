<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\ContactInquiry;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Webseite\Application\ContactInquiry\GetContactInquiry\GetContactInquiry;
use Yoga\Modules\Webseite\Application\ContactInquiry\UpdateContactInquiry\Request as UpdateRequest;
use Yoga\Modules\Webseite\Application\ContactInquiry\UpdateContactInquiry\UpdateContactInquiry;

#[Layout('verwaltung::layouts.app')]
final class ContactInquiryDetail extends Component
{
    public string $inquiryId = '';

    /**
     * @var object{id: string, empfangen_am: string, name: string, email: string, telefon: string|null, anlass: string|null, nachricht: string, status: string, notiz: string|null}|null
     */
    public ?object $inquiry = null;

    public string $status = '';

    public string $note = '';

    public bool $saved = false;

    public string $error = '';

    public function mount(string $id, GetContactInquiry $query): void
    {
        $this->inquiryId = $id;
        $this->inquiry = $query->execute($id);

        if ($this->inquiry !== null) {
            $this->status = $this->inquiry->status;
            $this->note = $this->inquiry->notiz ?? '';
        }
    }

    public function save(UpdateContactInquiry $operation, GetContactInquiry $query): void
    {
        $this->saved = false;
        $this->error = '';

        $result = $operation->execute(new UpdateRequest(
            inquiryId: $this->inquiryId,
            status: $this->status,
            note: $this->note === '' ? null : $this->note,
        ));

        if ($result->isFailure()) {
            $this->error = 'Die Anfrage konnte nicht gespeichert werden: '.($result->error()['code'] ?? 'unknown');

            return;
        }

        $this->saved = true;
        $this->inquiry = $query->execute($this->inquiryId);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('webseite::ContactInquiry.contact-inquiry-detail');
    }
}
