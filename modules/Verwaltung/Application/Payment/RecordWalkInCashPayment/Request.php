<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordWalkInCashPayment;

final readonly class Request
{
    public function __construct(
        public string $activityId,
        public string $firstName,
        public string $lastName,
        public ?string $email,
        public string $amount,
        public ?string $paidAt = null,
        public ?string $receiptNumber = null,
        public ?string $issuedAt = null,
    ) {
    }
}
