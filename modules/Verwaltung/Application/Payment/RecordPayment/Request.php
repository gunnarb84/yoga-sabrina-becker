<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordPayment;

final readonly class Request
{
    public function __construct(
        public string $registrationId,
        public string $method,
        public string $amount,
        public ?string $paidAt,
        public string $recipient,
    ) {
    }
}
