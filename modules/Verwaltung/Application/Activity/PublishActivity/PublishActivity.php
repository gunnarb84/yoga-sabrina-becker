<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\PublishActivity;

use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Platform\Shared\Application\Result;

final readonly class PublishActivity
{
    public function execute(Request $request): Result
    {
        $activity = Activity::findById($request->activityId);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        $activity->publish();
        $activity->save();

        return Result::success();
    }
}
