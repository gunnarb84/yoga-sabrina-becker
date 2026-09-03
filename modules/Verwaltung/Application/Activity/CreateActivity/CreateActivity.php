<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\CreateActivity;

use Illuminate\Support\Str;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class CreateActivity
{
    public function execute(Request $request): Result
    {
        $activity = new Activity([
            'typ' => $request->type->value,
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

        $activity->slug = $this->uniqueSlug($request->title, $activity->getAttribute('id'));
        $activity->save();

        return Result::success(new Response($activity->getAttribute('id')));
    }

    private function uniqueSlug(string $title, string $id): string
    {
        return Str::slug($title) . '-' . substr($id, 0, 8);
    }
}
