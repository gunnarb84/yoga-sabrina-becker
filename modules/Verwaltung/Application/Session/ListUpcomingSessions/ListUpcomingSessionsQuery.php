<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Session\ListUpcomingSessions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

/**
 * Liefert die Termine vom heutigen Datum bis zum Ende des laufenden Monats,
 * aufsteigend nach Beginn, mit Belegung der jeweiligen Veranstaltung.
 */
final readonly class ListUpcomingSessionsQuery
{
    /**
     * @return list<object{id: string, aktivitaetId: string, beginn: string, titel: string, maxParticipants: int, confirmed: int, freeSeats: int}>
     */
    public function execute(): array
    {
        $beginn = Carbon::now()->startOfDay();
        $ende = Carbon::now()->endOfMonth()->addDay()->startOfDay();

        $rows = DB::table('verwaltung_termine as t')
            ->join('verwaltung_aktivitaeten as a', 'a.id', '=', 't.aktivitaet_id')
            ->leftJoinSub(
                DB::table('verwaltung_anmeldungen')
                    ->selectRaw('aktivitaet_id, count(*) AS bestaetigt')
                    ->where('status', 'bestaetigt')
                    ->groupBy('aktivitaet_id'),
                'anm',
                'anm.aktivitaet_id',
                '=',
                'a.id',
            )
            ->select([
                't.id',
                'a.id AS aktivitaet_id',
                't.beginn',
                'a.titel',
                'a.maximale_teilnehmerzahl',
                'anm.bestaetigt',
            ])
            ->where('t.beginn', '>=', $beginn)
            ->where('t.beginn', '<', $ende)
            ->orderBy('t.beginn')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            if (! is_string($row->id) || ! is_string($row->aktivitaet_id)) {
                continue;
            }

            $maxParticipants = DbValue::int($row->maximale_teilnehmerzahl);
            $confirmed = DbValue::int($row->bestaetigt ?? 0);

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'aktivitaetId' => Uuid::fromBytes($row->aktivitaet_id)->toString(),
                'beginn' => DbValue::string($row->beginn),
                'titel' => DbValue::string($row->titel),
                'maxParticipants' => $maxParticipants,
                'confirmed' => $confirmed,
                'freeSeats' => max(0, $maxParticipants - $confirmed),
            ];
        }

        return $result;
    }
}
