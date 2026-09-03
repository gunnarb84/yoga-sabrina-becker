<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CourseTemplate\CreateCourseTemplate;

final readonly class Response
{
    public function __construct(public string $templateId)
    {
    }
}
