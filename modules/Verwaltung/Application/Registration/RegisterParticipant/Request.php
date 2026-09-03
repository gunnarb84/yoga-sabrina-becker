<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant;

final readonly class Request
{
    public function __construct(
        public string $activityId,
        public string $participantId,
        public string $paymentMethod,
    ) {
    }
}
