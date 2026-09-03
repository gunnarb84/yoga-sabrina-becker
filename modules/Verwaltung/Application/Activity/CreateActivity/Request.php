<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\CreateActivity;

use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;

final readonly class Request
{
    public function __construct(
        public ActivityType $type,
        public string $title,
        public ?string $shortDescription,
        public ?string $longDescription,
        public string $price,
        public int $maxParticipants,
        public ?string $image,
    ) {
    }
}
