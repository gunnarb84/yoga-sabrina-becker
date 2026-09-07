<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\PublicPublishedActivities;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\Session\Session;

final readonly class PublishedActivitiesQuery
{
    /**
     * @return list<object>
     */
    public function execute(int $limit = 6): array
    {
        $now = now();

        $activityIdsWithFutureSessions = DB::table('verwaltung_termine')
            ->where('beginn', '>=', $now)
            ->distinct()
            ->pluck('aktivitaet_id');

        if ($activityIdsWithFutureSessions->isEmpty()) {
            return [];
        }

        $activities = Activity::where('veroeffentlicht', true)
            ->where('status', ActivityStatus::Published->value)
            ->whereIn('id', $activityIdsWithFutureSessions)
            ->orderBy('titel')
            ->limit($limit)
            ->get();

        $items = [];

        foreach ($activities as $activity) {
            $activityIdBytes = Uuid::fromString($activity->id)->getBytes();

            $confirmedCount = DB::table('verwaltung_anmeldungen')
                ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                ->where('status', RegistrationStatus::Confirmed->value)
                ->count();

            $freeSeats = max(0, $activity->maximale_teilnehmerzahl - $confirmedCount);

            $waitingListCount = DB::table('verwaltung_anmeldungen')
                ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                ->where('status', RegistrationStatus::WaitingList->value)
                ->count();

            $nextSession = Session::select('beginn')
                ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                ->where('beginn', '>=', $now)
                ->orderBy('beginn')
                ->first();

            $type = $activity->typ;

            $items[] = (object) [
                'id' => $activity->id,
                'slug' => $activity->slug,
                'typ' => $type->value,
                'typLabel' => $this->typeLabel($type),
                'titel' => $activity->titel,
                'kurzbeschreibung' => $activity->kurzbeschreibung,
                'preis' => $activity->preis,
                'bild' => $activity->bild,
                'naechster_termin' => $nextSession?->beginn?->format('Y-m-d H:i:s'),
                'freie_plaetze' => $freeSeats,
                'warteliste_anzahl' => $waitingListCount,
                'ausgebucht' => $freeSeats === 0,
                'buchbar' => $freeSeats > 0,
            ];
        }

        return $items;
    }

    private function typeLabel(ActivityType $type): string
    {
        return match ($type) {
            ActivityType::Course => 'Kurs',
            ActivityType::Event => 'Event',
            ActivityType::Workshop => 'Workshop',
        };
    }
}
