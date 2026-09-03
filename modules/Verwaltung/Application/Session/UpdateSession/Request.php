<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Session\UpdateSession;

final readonly class Request
{
    public function __construct(
        public string $sessionId,
        public string $startsAt,
        public string $endsAt,
        public ?string $location,
        public ?string $note,
    ) {
    }
}
