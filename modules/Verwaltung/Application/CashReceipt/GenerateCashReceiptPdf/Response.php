<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf;

final readonly class Response
{
    public function __construct(
        public ?string $content,
        public string $filename,
    ) {
    }
}
