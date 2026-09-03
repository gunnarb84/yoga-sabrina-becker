<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Session\DeleteSession;

final readonly class Request
{
    public function __construct(public string $sessionId)
    {
    }
}
