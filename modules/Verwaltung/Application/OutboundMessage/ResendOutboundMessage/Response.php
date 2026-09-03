<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage;

final readonly class Response
{
    public function __construct(
        public string $newMessageId,
    ) {
    }
}
