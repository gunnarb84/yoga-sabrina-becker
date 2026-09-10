<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Payment;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch\Item as BatchItem;
use Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch\RecordCashPaymentBatch as BatchOperation;
use Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch\Request as BatchRequest;
use Yoga\Modules\Verwaltung\Application\Registration\ListOpenCashRegistrations\ListOpenCashRegistrationsQuery;

#[Layout('verwaltung::layouts.app')]
final class BulkCashPayments extends Component
{
    public string $activityId = '';

    /**
     * @var list<array{registrationId: string, empfaenger: string, betrag: string, waehrung: string, selected: bool, paidAt: string}>
     */
    public array $items = [];

    public string $message = '';

    public string $error = '';

    public function mount(string $id, ListOpenCashRegistrationsQuery $query): void
    {
        $this->activityId = $id;
        $this->loadItems($query);
    }

    public function save(BatchOperation $operation, ListOpenCashRegistrationsQuery $query): void
    {
        $this->message = '';
        $this->error = '';

        $selected = array_values(array_filter($this->items, fn (array $item): bool => $item['selected'] === true));

        if ($selected === []) {
            $this->error = 'Bitte wählen Sie mindestens eine Anmeldung aus.';

            return;
        }

        $items = [];

        foreach ($selected as $item) {
            $items[] = new BatchItem(
                $item['registrationId'],
                $item['paidAt'] !== '' ? $item['paidAt'] : null,
            );
        }

        $result = $operation->execute(new BatchRequest($this->activityId, $items));

        if ($result->isFailure()) {
            $this->error = 'Die Massenerfassung konnte nicht gestartet werden.';

            return;
        }

        $response = $result->unwrap();
        $this->message = $response->recordedCount.' Zahlung(en) wurden erfasst'
            .($response->skippedCount > 0 ? ', '.$response->skippedCount.' wurde(n) übersprungen.' : '.');

        $this->loadItems($query);
    }

    private function loadItems(ListOpenCashRegistrationsQuery $query): void
    {
        $rows = $query->execute($this->activityId);

        $this->items = [];

        foreach ($rows as $row) {
            $this->items[] = [
                'registrationId' => $row->registrationId,
                'empfaenger' => $row->empfaenger,
                'betrag' => $row->betrag,
                'waehrung' => $row->waehrung,
                'selected' => false,
                'paidAt' => now()->format('Y-m-d\TH:i'),
            ];
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Payment.bulk-cash-payments');
    }
}
