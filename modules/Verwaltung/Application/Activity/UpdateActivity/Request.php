<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity;

final readonly class Request
{
    public function __construct(
        public string $activityId,
        public string $type,
        public string $title,
        public ?string $shortDescription,
        public ?string $longDescription,
        public string $price,
        public int $maxParticipants,
        public ?string $image,
    ) {
    }
}
