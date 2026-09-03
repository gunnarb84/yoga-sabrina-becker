<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity;

final readonly class Response
{
    public function __construct(
        public string $activityId,
    ) {
    }
}
