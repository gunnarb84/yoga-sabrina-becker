<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf;

final readonly class Request
{
    public function __construct(
        public string $invoiceId,
    ) {
    }
}
