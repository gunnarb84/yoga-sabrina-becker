<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Session\CreateSession;

use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Session\Session;
use Yoga\Platform\Shared\Application\Result;

final readonly class CreateSession
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $activity = Activity::findById($request->activityId);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        $session = new Session([
            'aktivitaet_id' => $request->activityId,
            'beginn' => $request->startsAt,
            'ende' => $request->endsAt,
            'ort' => $request->location,
            'hinweis' => $request->note,
        ]);

        $session->save();

        return Result::success(new Response($session->id));
    }
}
