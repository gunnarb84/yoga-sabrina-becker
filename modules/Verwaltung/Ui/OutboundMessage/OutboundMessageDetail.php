<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\OutboundMessage;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\OutboundMessageDetail\OutboundMessageDetailQuery;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage\Request as ResendOutboundMessageRequest;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage\ResendOutboundMessage as ResendOutboundMessageOperation;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;

#[Layout('verwaltung::layouts.app')]
final class OutboundMessageDetail extends Component
{
    public string $messageId = '';

    public ?object $message = null;

    public string $status = '';

    public bool $resent = false;

    public function mount(string $id, OutboundMessageDetailQuery $query): void
    {
        $this->messageId = $id;
        $this->message = $query->execute($id);
        $this->status = $this->message !== null ? $this->message->status : '';
    }

    public function resend(ResendOutboundMessageOperation $operation, OutboundMessageDetailQuery $query): void
    {
        $result = $operation->execute(new ResendOutboundMessageRequest($this->messageId));

        if ($result->isSuccess()) {
            $this->resent = true;
            $this->message = $query->execute($this->messageId);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('verwaltung::outbound-message.detail', [
            'canResend' => in_array($this->status, [OutboundMessageStatus::Pending->value, OutboundMessageStatus::Failed->value], true),
        ]);
    }
}
