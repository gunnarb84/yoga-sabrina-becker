<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\ContactInquiry;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Webseite\Application\ContactInquiry\ListContactInquiries\ListContactInquiries;

#[Layout('verwaltung::layouts.app')]
final class ContactInquiryList extends Component
{
    public string $statusFilter = '';

    /**
     * @var list<object{id: string, empfangen_am: string, name: string, email: string, anlass: string|null, status: string}>
     */
    public array $inquiries = [];

    public function mount(ListContactInquiries $query): void
    {
        $this->inquiries = $query->execute();
    }

    public function search(ListContactInquiries $query): void
    {
        $this->inquiries = $query->execute(statusFilter: $this->statusFilter);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('webseite::ContactInquiry.contact-inquiry-list');
    }
}
