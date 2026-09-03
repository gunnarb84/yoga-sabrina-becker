<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CourseTemplate\CreateCourseTemplate;

final readonly class Request
{
    public function __construct(
        public string $title,
        public string $weekday,
        public string $startTime,
        public int $durationMinutes,
        public int $sessionCount,
        public string $price,
        public int $maxParticipants,
        public ?string $shortDescription = null,
        public ?string $longDescription = null,
        public ?string $location = null,
        public string $currency = 'EUR',
    ) {
    }
}
