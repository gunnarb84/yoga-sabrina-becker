<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\OutboundMessage;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\OutboundMessages\OutboundMessagesQuery;

#[Layout('verwaltung::layouts.app')]
final class OutboundMessageList extends Component
{
    public string $statusFilter = '';

    public string $recipientFilter = '';

    /**
     * @var list<object{id: string, anmeldung_id: string|null, empfaenger: string, betreff: string, status: string, versendet_am: string|null}>
     */
    public array $messages = [];

    public function mount(OutboundMessagesQuery $query): void
    {
        $this->messages = $query->execute();
    }

    public function search(OutboundMessagesQuery $query): void
    {
        $this->messages = $query->execute(
            statusFilter: $this->statusFilter,
            recipientFilter: $this->recipientFilter,
        );
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('verwaltung::outbound-message.list');
    }
}
