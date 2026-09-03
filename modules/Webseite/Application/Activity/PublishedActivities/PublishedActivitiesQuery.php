<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\Activity\PublishedActivities;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;

final readonly class PublishedActivitiesQuery
{
    /**
     * @return list<object{
     *     id: string,
     *     slug: string,
     *     typ: string,
     *     typLabel: string,
     *     titel: string,
     *     kurzbeschreibung: string|null,
     *     preis: string,
     *     bild: string|null,
     *     naechster_termin: string|null,
     *     freie_plaetze: int,
     *     warteliste_anzahl: int,
     *     ausgebucht: bool
     * }>
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

        $rows = DB::table('verwaltung_aktivitaeten')
            ->select([
                'id',
                'slug',
                'typ',
                'titel',
                'kurzbeschreibung',
                'preis',
                'maximale_teilnehmerzahl',
                'bild',
            ])
            ->where('veroeffentlicht', true)
            ->where('status', 'veroeffentlicht')
            ->whereIn('id', $activityIdsWithFutureSessions)
            ->orderBy('titel')
            ->limit($limit)
            ->get();

        return $rows->map(function (object $row) use ($now): object {
            $activityIdBytes = $row->id;

            $confirmedCount = DB::table('verwaltung_anmeldungen')
                ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                ->where('status', RegistrationStatus::Confirmed->value)
                ->count();

            $freeSeats = max(0, (int) $row->maximale_teilnehmerzahl - (int) $confirmedCount);

            $waitingListCount = DB::table('verwaltung_anmeldungen')
                ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                ->where('status', RegistrationStatus::WaitingList->value)
                ->count();

            $nextSession = DB::table('verwaltung_termine')
                ->select('beginn')
                ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                ->where('beginn', '>=', $now)
                ->orderBy('beginn')
                ->first();

            $type = ActivityType::tryFrom($row->typ);

            return (object) [
                'id' => Uuid::fromBytes($activityIdBytes)->toString(),
                'slug' => $row->slug,
                'typ' => $row->typ,
                'typLabel' => $this->typeLabel($type),
                'titel' => $row->titel,
                'kurzbeschreibung' => $row->kurzbeschreibung,
                'preis' => $row->preis,
                'bild' => $row->bild,
                'naechster_termin' => $nextSession?->beginn,
                'freie_plaetze' => $freeSeats,
                'warteliste_anzahl' => (int) $waitingListCount,
                'ausgebucht' => $freeSeats === 0,
            ];
        })->toArray();
    }

    private function typeLabel(?ActivityType $type): string
    {
        return match ($type) {
            ActivityType::Course => 'Kurs',
            ActivityType::Event => 'Event',
            ActivityType::Workshop => 'Workshop',
            default => $type?->value ?? '',
        };
    }
}
