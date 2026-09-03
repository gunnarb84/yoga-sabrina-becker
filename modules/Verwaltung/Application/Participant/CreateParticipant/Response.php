<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant;

final readonly class Response
{
    public function __construct(public string $participantId)
    {
    }
}
