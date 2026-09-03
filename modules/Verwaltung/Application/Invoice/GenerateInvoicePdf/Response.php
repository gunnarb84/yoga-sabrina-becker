<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\GenerateInvoicePdf;

final readonly class Response
{
    public function __construct(
        public string $content,
        public string $filename,
    ) {
    }
}
