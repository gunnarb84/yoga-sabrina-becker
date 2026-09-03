<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage;

final readonly class Request
{
    public function __construct(
        public string $messageId,
    ) {
    }
}
