<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptListPdf;

final readonly class Request
{
    public function __construct(
        public string $monat,
    ) {
    }
}
