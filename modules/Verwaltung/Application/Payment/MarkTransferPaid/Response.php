<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid;

use Yoga\Modules\Verwaltung\Domain\Invoice\InvoiceStatus;

final readonly class Response
{
    public function __construct(
        public string $paymentId,
        public InvoiceStatus $status,
    ) {
    }
}
