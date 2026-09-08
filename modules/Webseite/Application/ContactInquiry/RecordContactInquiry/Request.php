<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry;

final readonly class Request
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $topic,
        public string $message,
        public string $honeypot,
        public string $ipAddress,
    ) {
    }
}
