<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal;

final readonly class Request
{
    public function __construct(
        public string $date,
        public string $amount,
        public string $purpose,
        public ?string $externalReference = null,
    ) {
    }
}
