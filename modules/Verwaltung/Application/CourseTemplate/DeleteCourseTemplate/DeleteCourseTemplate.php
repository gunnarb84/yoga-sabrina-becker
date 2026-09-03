<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CourseTemplate\DeleteCourseTemplate;

use Yoga\Modules\Verwaltung\Domain\CourseTemplate\CourseTemplate;
use Yoga\Platform\Shared\Application\Result;

final readonly class DeleteCourseTemplate
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $template = CourseTemplate::findById($request->templateId);

        if ($template === null) {
            return Result::failure('course_template.not_found');
        }

        $template->delete();

        return Result::success(new Response($request->templateId));
    }
}
