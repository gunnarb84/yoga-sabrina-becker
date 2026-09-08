<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Payment;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment as RecordPaymentOperation;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPaymentPrefill\RecordPaymentPrefillQuery;

#[Layout('verwaltung::layouts.app')]
final class RecordPayment extends Component
{
    public string $registrationId = '';

    public string $activityId = '';

    public string $amount = '';

    public string $paidAt = '';

    public string $recipient = '';

    public function mount(string $id, RecordPaymentPrefillQuery $prefillQuery): void
    {
        $this->registrationId = $id;
        $prefill = $prefillQuery->execute($id);

        if ($prefill === null || $prefill->fall === 'cancelled') {
            $this->redirect(route('verwaltung.activities'), navigate: true);

            return;
        }

        if ($prefill->fall !== 'editable') {
            $this->redirect(route('verwaltung.activity.registrations', ['id' => $prefill->aktivitaet_id]), navigate: true);

            return;
        }

        $this->activityId = $prefill->aktivitaet_id;
        $this->amount = $prefill->betrag;
        $this->paidAt = now()->format('Y-m-d\TH:i');
        $this->recipient = $prefill->empfaenger;
    }

    public function save(RecordPaymentOperation $operation): void
    {
        $result = $operation->execute(new RecordPaymentRequest(
            registrationId: $this->registrationId,
            method: 'bar',
            amount: $this->amount,
            paidAt: $this->paidAt,
            recipient: $this->recipient,
        ));

        if ($result->isFailure()) {
            return;
        }

        $this->redirect(route('verwaltung.activity.registrations', ['id' => $this->activityId]), navigate: true);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('verwaltung::Payment.record-payment');
    }
}
