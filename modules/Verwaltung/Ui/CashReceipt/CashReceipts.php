<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CashReceipt;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\CashReceipt\ListCashReceipts\ListCashReceiptsQuery;

#[Layout('verwaltung::layouts.app')]
final class CashReceipts extends Component
{
    /**
     * @var list<object{id: string, nummer: string, ausgestellt_am: string, empfaenger: string, betrag: string, waehrung: string}>
     */
    public array $receipts = [];

    public string $nummerFilter = '';

    public string $empfaengerFilter = '';

    public function mount(ListCashReceiptsQuery $query): void
    {
        $this->receipts = $query->execute();
    }

    public function updated(string $name, string $value, ListCashReceiptsQuery $query): void
    {
        $this->receipts = $query->execute($this->nummerFilter, $this->empfaengerFilter);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::CashReceipt.cash-receipts');
    }
}
