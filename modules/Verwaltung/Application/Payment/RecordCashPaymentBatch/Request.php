<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch;

final readonly class Request
{
    /**
     * @param  list<Item>  $items
     */
    public function __construct(
        public string $activityId,
        public array $items,
    ) {
    }
}
