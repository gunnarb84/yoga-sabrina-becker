<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\SendInvoice;

final readonly class Request
{
    public function __construct(
        public string $invoiceId,
        public string $subject,
        public string $body,
    ) {
    }
}
