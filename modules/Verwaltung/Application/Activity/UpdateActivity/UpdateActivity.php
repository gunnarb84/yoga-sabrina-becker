<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity;

use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class UpdateActivity
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $activity = Activity::findById($request->activityId);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        if ($activity->status === ActivityStatus::Completed) {
            return Result::failure('activity.already_completed');
        }

        if ($activity->status === ActivityStatus::Cancelled) {
            return Result::failure('activity.already_cancelled');
        }

        if ($request->title === '') {
            return Result::failure('activity.title_empty');
        }

        if ((float) $request->price < 0) {
            return Result::failure('activity.price_negative');
        }

        if ($request->maxParticipants < 1) {
            return Result::failure('activity.max_participants_too_low');
        }

        $activity->typ = $request->type;
        $activity->titel = $request->title;
        $activity->kurzbeschreibung = $request->shortDescription;
        $activity->langbeschreibung = $request->longDescription;
        $activity->preis = $request->price;
        $activity->maximale_teilnehmerzahl = $request->maxParticipants;
        $activity->bild = $request->image;

        $activity->save();

        return Result::success(new Response($activity->id));
    }
}
