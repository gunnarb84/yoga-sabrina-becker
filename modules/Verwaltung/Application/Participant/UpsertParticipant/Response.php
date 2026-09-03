<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant;

final readonly class Response
{
    public function __construct(
        public string $participantId,
        public bool $wasCreated,
    ) {
    }
}
