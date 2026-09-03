<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordPayment;

use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;

final readonly class Request
{
    public function __construct(
        public string $registrationId,
        public PaymentMethod $method,
        public string $amount,
        public ?string $paidAt,
        public string $recipient,
    ) {
    }
}
