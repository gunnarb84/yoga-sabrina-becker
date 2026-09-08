<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage;

final readonly class Request
{
    /**
     * @param  array{filename: string, content: string, mime: string}|null  $attachment
     */
    public function __construct(
        public string $recipient,
        public string $subject,
        public string $html,
        public ?string $anmeldungId = null,
        public ?array $attachment = null,
    ) {
    }
}
