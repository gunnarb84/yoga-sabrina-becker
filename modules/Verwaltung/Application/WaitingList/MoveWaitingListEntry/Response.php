<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\WaitingList\MoveWaitingListEntry;

final readonly class Response
{
    public function __construct(public string $registrationId)
    {
    }
}
