<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\UnpublishActivity;

use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Platform\Shared\Application\Result;

final readonly class UnpublishActivity
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $activity = Activity::findById($request->activityId);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        if ($activity->hasRegistrations()) {
            return Result::failure('activity.has_registrations');
        }

        $activity->unpublish();
        $activity->save();

        return Result::success(new Response($activity->id));
    }
}
