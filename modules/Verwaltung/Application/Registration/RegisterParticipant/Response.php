<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant;

final readonly class Response
{
    public function __construct(
        public string $registrationId,
        public bool $onWaitingList,
        public ?string $invoiceId = null,
    ) {
    }
}
