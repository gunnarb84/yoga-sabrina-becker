<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal;

final readonly class Response
{
    public function __construct(
        public string $withdrawalId,
        public float $amount,
        public string $purpose,
    ) {
    }
}
