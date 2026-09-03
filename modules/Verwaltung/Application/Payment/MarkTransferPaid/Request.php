<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid;

final readonly class Request
{
    public function __construct(
        public string $invoiceId,
        public ?string $userId = null,
    ) {
    }
}
