<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\PublishActivity;

use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class PublishActivity
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $activity = Activity::findById($request->activityId);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        if ($activity->status === ActivityStatus::Published) {
            return Result::failure('activity.already_published');
        }

        $activity->publish();
        $activity->save();

        return Result::success(new Response($activity->id));
    }
}
