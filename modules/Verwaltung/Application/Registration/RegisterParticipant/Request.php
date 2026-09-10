<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant;

final readonly class Request
{
    /** Herkunft: Anmeldung über den Online-Anmeldeprozess der Webseite. */
    public const SOURCE_WEBSITE = 'webseite';

    /** Herkunft: Anmeldung in der Verwaltung angelegt. */
    public const SOURCE_ADMINISTRATION = 'verwaltung';

    public function __construct(
        public string $activityId,
        public string $participantId,
        public string $paymentMethod,
        public string $source = self::SOURCE_WEBSITE,
    ) {
    }
}
