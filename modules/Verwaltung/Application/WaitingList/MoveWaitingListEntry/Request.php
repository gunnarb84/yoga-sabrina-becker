<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\WaitingList\MoveWaitingListEntry;

final readonly class Request
{
    public function __construct(
        public string $registrationId,
        public string $direction,
    ) {
    }
}
