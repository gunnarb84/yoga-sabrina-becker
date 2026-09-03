<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\Activities;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final readonly class ActivitiesQuery
{
    /**
     * @return list<object{id: string, typ: string, titel: string, kurzbeschreibung: string|null, preis: string, maximale_teilnehmerzahl: int, status: string, veroeffentlicht: bool, anzahl_termine: int}>
     */
    public function execute(int $limit = 100): array
    {
        $rows = DB::table('verwaltung_aktivitaeten')
            ->select([
                'verwaltung_aktivitaeten.id',
                'verwaltung_aktivitaeten.typ',
                'verwaltung_aktivitaeten.titel',
                'verwaltung_aktivitaeten.kurzbeschreibung',
                'verwaltung_aktivitaeten.preis',
                'verwaltung_aktivitaeten.maximale_teilnehmerzahl',
                'verwaltung_aktivitaeten.status',
                'verwaltung_aktivitaeten.veroeffentlicht',
            ])
            ->orderBy('verwaltung_aktivitaeten.angelegt_am', 'desc')
            ->limit($limit)
            ->get();

        return $rows->map(function (object $row): object {
            $sessionCount = DB::table('verwaltung_termine')
                ->where('aktivitaet_id', $row->id)
                ->count();

            return (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'typ' => $row->typ,
                'titel' => $row->titel,
                'kurzbeschreibung' => $row->kurzbeschreibung,
                'preis' => $row->preis,
                'maximale_teilnehmerzahl' => (int) $row->maximale_teilnehmerzahl,
                'status' => $row->status,
                'veroeffentlicht' => (bool) $row->veroeffentlicht,
                'anzahl_termine' => $sessionCount,
            ];
        })->toArray();
    }
}
