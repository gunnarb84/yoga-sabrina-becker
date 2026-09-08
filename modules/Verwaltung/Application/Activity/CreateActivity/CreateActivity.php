<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\CreateActivity;

use Illuminate\Support\Str;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Platform\Shared\Application\Result;

final readonly class CreateActivity
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $type = ActivityType::tryFrom($request->type);

        if ($type === null) {
            return Result::failure('activity.type_invalid');
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

        $activity = new Activity([
            'typ' => $type->value,
            'titel' => $request->title,
            'kurzbeschreibung' => $request->shortDescription,
            'langbeschreibung' => $request->longDescription,
            'preis' => $request->price,
            'maximale_teilnehmerzahl' => $request->maxParticipants,
            'veroeffentlicht' => false,
            'status' => ActivityStatus::Draft->value,
            'bild' => $request->image,
        ]);

        $activity->save();

        $activity->slug = $this->uniqueSlug($request->title, $activity->id);
        $activity->save();

        return Result::success(new Response($activity->id));
    }

    private function uniqueSlug(string $title, string $id): string
    {
        return Str::slug($title) . '-' . substr($id, 0, 8);
    }
}
