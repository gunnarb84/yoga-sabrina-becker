<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation;

final readonly class Request
{
    public function __construct(
        public string $registrationId,
    ) {
    }
}
