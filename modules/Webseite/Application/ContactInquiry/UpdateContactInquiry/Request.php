<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\ContactInquiry\UpdateContactInquiry;

final readonly class Request
{
    public function __construct(
        public string $inquiryId,
        public string $status,
        public ?string $note,
    ) {
    }
}
