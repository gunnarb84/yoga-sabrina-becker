<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CashReceipt;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\CashReceipt\ListCashMovements\ListCashMovementsQuery;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\RecordCashWithdrawal as RecordWithdrawalOperation;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\Request as RecordWithdrawalRequest;

#[Layout('verwaltung::layouts.app')]
final class CashReceipts extends Component
{
    public bool $showWithdrawalForm = false;

    public string $withdrawalDate = '';

    public string $withdrawalAmount = '';

    public string $withdrawalPurpose = '';

    public string $withdrawalExternalReference = '';

    public string $message = '';

    public bool $withdrawalRecorded = false;

    /**
     * @var list<object{id: string, typ: string, richtung: string, datum: string, kennung: string, beschreibung: string, betrag: string, waehrung: string, bestand: string}>
     */
    public array $movements = [];

    public string $nummerFilter = '';

    public string $empfaengerFilter = '';

    public function mount(ListCashMovementsQuery $query): void
    {
        $this->movements = $query->execute();
        $this->withdrawalDate = now()->format('Y-m-d');
    }

    public function updated(string $name, string $value, ListCashMovementsQuery $query): void
    {
        $this->movements = $query->execute($this->nummerFilter, $this->empfaengerFilter);
    }

    public function toggleWithdrawalForm(): void
    {
        $this->showWithdrawalForm = ! $this->showWithdrawalForm;
    }

    public function saveWithdrawal(RecordWithdrawalOperation $operation, ListCashMovementsQuery $query): void
    {
        $this->message = '';

        $result = $operation->execute(new RecordWithdrawalRequest(
            date: $this->withdrawalDate,
            amount: $this->withdrawalAmount,
            purpose: $this->withdrawalPurpose,
            externalReference: $this->withdrawalExternalReference !== '' ? $this->withdrawalExternalReference : null,
        ));

        if ($result->isFailure()) {
            $this->message = $this->messageFor((string) ($result->error()['code'] ?? 'cash_withdrawal.failed'));

            return;
        }

        $this->withdrawalRecorded = true;
        $this->resetWithdrawalForm();
        $this->showWithdrawalForm = false;
        $this->movements = $query->execute($this->nummerFilter, $this->empfaengerFilter);
    }

    private function resetWithdrawalForm(): void
    {
        $this->withdrawalDate = now()->format('Y-m-d');
        $this->withdrawalAmount = '';
        $this->withdrawalPurpose = '';
        $this->withdrawalExternalReference = '';
    }

    private function messageFor(string $code): string
    {
        return match ($code) {
            'cash_withdrawal.amount_invalid' => 'Der Betrag muss größer als null sein.',
            'cash_withdrawal.purpose_required' => 'Der Zweck ist ein Pflichtfeld.',
            'cash_withdrawal.date_invalid' => 'Das Datum ist ungültig.',
            default => 'Fehler bei der Erfassung: '.$code,
        };
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::CashReceipt.cash-receipts');
    }
}
