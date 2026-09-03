<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Invoice;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Invoice\Invoices\InvoicesQuery;
use Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid\MarkTransferPaid as MarkTransferPaidOperation;
use Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid\Request as MarkTransferPaidRequest;

#[Layout('verwaltung::layouts.app')]
final class Invoices extends Component
{
    /**
     * @var list<object{id: string, nummer: string, ausgestellt_am: string, empfaenger: string, betrag: string, waehrung: string, status: string, zahlung_id: string}>
     */
    public array $invoices = [];

    public function mount(InvoicesQuery $query): void
    {
        $this->invoices = $query->execute();
    }

    public function toggleStatus(string $id, MarkTransferPaidOperation $operation, InvoicesQuery $query): void
    {
        $operation->execute(new MarkTransferPaidRequest(invoiceId: $id));
        $this->invoices = $query->execute();
    }

    public function render()
    {
        return view('verwaltung::invoice.invoices');
    }
}
