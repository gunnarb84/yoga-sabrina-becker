<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry;

final readonly class Response
{
    public function __construct(
        public ?string $inquiryId,
    ) {
    }
}
