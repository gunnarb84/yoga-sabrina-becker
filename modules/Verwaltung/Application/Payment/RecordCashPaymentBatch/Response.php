<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch;

final readonly class Response
{
    /**
     * @param  list<Outcome>  $outcomes
     */
    public function __construct(
        public array $outcomes,
        public int $recordedCount,
        public int $skippedCount,
    ) {
    }
}
