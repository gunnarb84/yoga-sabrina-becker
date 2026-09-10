<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CashReceipt;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\Activities\ActivitiesQuery;
use Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment\RecordWalkInCashPayment as RecordWalkInOperation;
use Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment\Request as RecordWalkInRequest;

#[Layout('verwaltung::layouts.app')]
final class RecordWalkInCashPayment extends Component
{
    public string $activityId = '';

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $amount = '';

    public string $paidAt = '';

    public bool $nachpflege = false;

    public string $receiptNumber = '';

    public string $issuedAt = '';

    public string $message = '';

    public bool $recorded = false;

    public string $receiptId = '';

    public string $receiptNumberRecorded = '';

    /**
     * @var list<object{id: string, typ: string, titel: string}>
     */
    public array $activities = [];

    public function mount(ActivitiesQuery $activitiesQuery): void
    {
        $this->activities = $activitiesQuery->execute();
        $this->paidAt = now()->format('Y-m-d\TH:i');
        $this->issuedAt = now()->format('Y-m-d');
    }

    public function save(RecordWalkInOperation $operation): void
    {
        $this->message = '';

        $result = $operation->execute(new RecordWalkInRequest(
            activityId: $this->activityId,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email !== '' ? $this->email : null,
            amount: $this->amount,
            paidAt: $this->paidAt,
            receiptNumber: $this->nachpflege && $this->receiptNumber !== '' ? $this->receiptNumber : null,
            issuedAt: $this->nachpflege && $this->issuedAt !== '' ? $this->issuedAt : null,
        ));

        if ($result->isFailure()) {
            $this->message = $this->messageFor((string) ($result->error()['code'] ?? 'payment.failed'));

            return;
        }

        $response = $result->unwrap();
        $this->recorded = true;
        $this->receiptId = $response->documentId;
        $this->receiptNumberRecorded = (string) $response->documentNumber;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->activityId = '';
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->amount = '';
        $this->paidAt = now()->format('Y-m-d\TH:i');
        $this->nachpflege = false;
        $this->receiptNumber = '';
        $this->issuedAt = now()->format('Y-m-d');
    }

    private function messageFor(string $code): string
    {
        return match ($code) {
            'activity.not_found' => 'Bitte eine Veranstaltung wählen.',
            'participant.name_required' => 'Vorname und Nachname sind Pflichtfelder.',
            'participant.email_invalid' => 'Die E-Mail-Adresse hat kein gültiges Format.',
            'payment.amount_invalid' => 'Der Betrag muss größer als null sein.',
            'registration.already_registered' => 'Für diese Person besteht bereits eine Anmeldung zu dieser Veranstaltung.',
            'receipt.number_invalid' => 'Die Belegnummer entspricht nicht dem Format JJJJ-NNNNN (z. B. 2026-00012).',
            'receipt.number_taken' => 'Diese Belegnummer ist bereits vergeben.',
            default => 'Fehler bei der Erfassung: '.$code,
        };
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('verwaltung::CashReceipt.record-walk-in-cash-payment');
    }
}
