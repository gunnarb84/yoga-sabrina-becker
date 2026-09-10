<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment;

final readonly class Response
{
    public function __construct(
        public string $paymentId,
        public string $documentNumber,
        public string $documentId,
    ) {
    }
}
