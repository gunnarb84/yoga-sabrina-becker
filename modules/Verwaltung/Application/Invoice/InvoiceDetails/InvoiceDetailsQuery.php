<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\InvoiceDetails;

use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;

final readonly class InvoiceDetailsQuery
{
    /**
     * @return object{id: string, nummer: string}|null
     */
    public function execute(string $invoiceId): ?object
    {
        $invoice = Invoice::findById($invoiceId);

        if ($invoice === null) {
            return null;
        }

        return (object) [
            'id' => $invoice->id,
            'nummer' => $invoice->nummer,
        ];
    }
}
