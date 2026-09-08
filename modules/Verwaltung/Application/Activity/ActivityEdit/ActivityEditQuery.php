<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\ActivityEdit;

use Yoga\Modules\Verwaltung\Domain\Activity\Activity;

final readonly class ActivityEditQuery
{
    /**
     * @return object{id: string, typ: string, titel: string, kurzbeschreibung: string, langbeschreibung: string, preis: string, maximale_teilnehmerzahl: int, status: string}|null
     */
    public function execute(string $activityId): ?object
    {
        $activity = Activity::findById($activityId);

        if ($activity === null) {
            return null;
        }

        return (object) [
            'id' => $activity->id,
            'typ' => $activity->typ->value,
            'titel' => $activity->titel,
            'kurzbeschreibung' => $activity->kurzbeschreibung ?? '',
            'langbeschreibung' => $activity->langbeschreibung ?? '',
            'preis' => $activity->preis,
            'maximale_teilnehmerzahl' => $activity->maximale_teilnehmerzahl,
            'status' => $activity->status->value,
        ];
    }
}
