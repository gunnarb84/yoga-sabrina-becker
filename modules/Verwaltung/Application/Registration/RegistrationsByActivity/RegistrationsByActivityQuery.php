<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\RegistrationsByActivity;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final readonly class RegistrationsByActivityQuery
{
    /**
     * @return list<object{id: string, teilnehmer_id: string, teilnehmer_name: string, email: string, status: string, angemeldet_am: string, zahlungsart: string, zahlungsstatus: string, rang: int|null}>
     */
    public function execute(string $activityId): array
    {
        $activityIdBytes = Uuid::fromString($activityId)->getBytes();

        $rows = DB::table('verwaltung_anmeldungen')
            ->select([
                'verwaltung_anmeldungen.id',
                'verwaltung_anmeldungen.teilnehmer_id',
                'verwaltung_anmeldungen.status',
                'verwaltung_anmeldungen.angemeldet_am',
                'verwaltung_anmeldungen.zahlungsart',
                'verwaltung_anmeldungen.zahlungsstatus',
                'verwaltung_teilnehmer.vorname',
                'verwaltung_teilnehmer.nachname',
                'verwaltung_teilnehmer.email',
                'verwaltung_warteliste.rang',
            ])
            ->whereRaw('verwaltung_anmeldungen.aktivitaet_id = ?', [$activityIdBytes])
            ->where('verwaltung_anmeldungen.status', '!=', 'storniert')
            ->leftJoin('verwaltung_teilnehmer', 'verwaltung_anmeldungen.teilnehmer_id', '=', 'verwaltung_teilnehmer.id')
            ->leftJoin('verwaltung_warteliste', 'verwaltung_anmeldungen.id', '=', 'verwaltung_warteliste.anmeldung_id')
            ->orderBy('verwaltung_anmeldungen.angemeldet_am')
            ->get();

        return $rows->map(function (object $row): object {
            return (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'teilnehmer_id' => Uuid::fromBytes($row->teilnehmer_id)->toString(),
                'teilnehmer_name' => trim($row->vorname . ' ' . $row->nachname),
                'email' => $row->email,
                'status' => $row->status,
                'angemeldet_am' => $row->angemeldet_am,
                'zahlungsart' => $row->zahlungsart,
                'zahlungsstatus' => $row->zahlungsstatus,
                'rang' => $row->rang === null ? null : (int) $row->rang,
            ];
        })->toArray();
    }
}
