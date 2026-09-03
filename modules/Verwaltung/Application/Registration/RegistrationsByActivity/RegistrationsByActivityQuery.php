<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\RegistrationsByActivity;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

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

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id) || ! is_string($row->teilnehmer_id)) {
                continue;
            }

            $firstName = DbValue::nullableString($row->vorname) ?? '';
            $lastName = DbValue::nullableString($row->nachname) ?? '';

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'teilnehmer_id' => Uuid::fromBytes($row->teilnehmer_id)->toString(),
                'teilnehmer_name' => trim($firstName . ' ' . $lastName),
                'email' => DbValue::string($row->email),
                'status' => DbValue::string($row->status),
                'angemeldet_am' => DbValue::string($row->angemeldet_am),
                'zahlungsart' => DbValue::string($row->zahlungsart),
                'zahlungsstatus' => DbValue::string($row->zahlungsstatus),
                'rang' => DbValue::nullableInt($row->rang),
            ];
        }

        return $result;
    }
}
