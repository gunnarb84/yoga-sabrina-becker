<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\AllRegistrations;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class AllRegistrationsQuery
{
    /**
     * @return list<object{id: string, aktivitaet_id: string, aktivitaet_titel: string, teilnehmer_id: string, teilnehmer_name: string, email: string, status: string, zahlungsart: string, herkunft: string, angemeldet_am: string, freie_plaetze: int, warteliste_anzahl: int}>
     */
    public function execute(?string $activityId = null, ?string $status = null, ?string $search = null, int $limit = 500): array
    {
        $query = DB::table('verwaltung_anmeldungen')
            ->select([
                'verwaltung_anmeldungen.id',
                'verwaltung_anmeldungen.aktivitaet_id',
                'verwaltung_anmeldungen.teilnehmer_id',
                'verwaltung_anmeldungen.status',
                'verwaltung_anmeldungen.angemeldet_am',
                'verwaltung_anmeldungen.zahlungsart',
                'verwaltung_anmeldungen.herkunft',
                'verwaltung_aktivitaeten.titel as aktivitaet_titel',
                'verwaltung_aktivitaeten.maximale_teilnehmerzahl',
                'verwaltung_teilnehmer.vorname',
                'verwaltung_teilnehmer.nachname',
                'verwaltung_teilnehmer.email',
            ])
            ->join('verwaltung_aktivitaeten', 'verwaltung_anmeldungen.aktivitaet_id', '=', 'verwaltung_aktivitaeten.id')
            ->join('verwaltung_teilnehmer', 'verwaltung_anmeldungen.teilnehmer_id', '=', 'verwaltung_teilnehmer.id')
            ->orderBy('verwaltung_anmeldungen.angemeldet_am', 'desc');

        if ($activityId !== null && $activityId !== '') {
            $query->whereRaw('verwaltung_anmeldungen.aktivitaet_id = ?', [Uuid::fromString($activityId)->getBytes()]);
        }

        if ($status !== null && $status !== '') {
            $query->where('verwaltung_anmeldungen.status', $status);
        }

        if ($search !== null && $search !== '') {
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('verwaltung_teilnehmer.vorname', 'like', $term)
                    ->orWhere('verwaltung_teilnehmer.nachname', 'like', $term)
                    ->orWhere('verwaltung_teilnehmer.email', 'like', $term)
                    ->orWhere('verwaltung_aktivitaeten.titel', 'like', $term);
            });
        }

        $rows = $query->limit($limit)->get();

        $activityIds = [];
        foreach ($rows as $row) {
            if (is_string($row->aktivitaet_id)) {
                $activityIds[] = $row->aktivitaet_id;
            }
        }

        $counts = $this->activityCounts(array_values(array_unique($activityIds)));

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id) || ! is_string($row->aktivitaet_id) || ! is_string($row->teilnehmer_id)) {
                continue;
            }

            $firstName = is_string($row->vorname) ? $row->vorname : '';
            $lastName = is_string($row->nachname) ? $row->nachname : '';
            $activityIdString = Uuid::fromBytes($row->aktivitaet_id)->toString();
            $count = $counts[$activityIdString] ?? ['confirmed' => 0, 'waiting' => 0, 'max' => 0];

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'aktivitaet_id' => $activityIdString,
                'aktivitaet_titel' => DbValue::string($row->aktivitaet_titel),
                'teilnehmer_id' => Uuid::fromBytes($row->teilnehmer_id)->toString(),
                'teilnehmer_name' => trim($firstName.' '.$lastName),
                'email' => DbValue::nullableString($row->email) ?? '',
                'status' => DbValue::string($row->status),
                'zahlungsart' => DbValue::string($row->zahlungsart),
                'herkunft' => DbValue::string($row->herkunft),
                'angemeldet_am' => DbValue::string($row->angemeldet_am),
                'freie_plaetze' => max(0, $count['max'] - $count['confirmed']),
                'warteliste_anzahl' => $count['waiting'],
            ];
        }

        return $result;
    }

    /**
     * @param  list<string>  $activityIds  binary UUIDs
     *
     * @return array<string, array{confirmed: int, waiting: int, max: int}>
     */
    private function activityCounts(array $activityIds): array
    {
        if ($activityIds === []) {
            return [];
        }

        $maxParticipants = DB::table('verwaltung_aktivitaeten')
            ->whereIn('id', $activityIds)
            ->pluck('maximale_teilnehmerzahl', 'id');

        $confirmed = DB::table('verwaltung_anmeldungen')
            ->selectRaw('aktivitaet_id, COUNT(*) as anzahl')
            ->whereIn('aktivitaet_id', $activityIds)
            ->where('status', RegistrationStatus::Confirmed->value)
            ->groupBy('aktivitaet_id')
            ->pluck('anzahl', 'aktivitaet_id');

        $waiting = DB::table('verwaltung_anmeldungen')
            ->selectRaw('aktivitaet_id, COUNT(*) as anzahl')
            ->whereIn('aktivitaet_id', $activityIds)
            ->where('status', RegistrationStatus::WaitingList->value)
            ->groupBy('aktivitaet_id')
            ->pluck('anzahl', 'aktivitaet_id');

        $counts = [];
        foreach ($activityIds as $id) {
            $key = Uuid::fromBytes($id)->toString();
            $counts[$key] = [
                'confirmed' => DbValue::int($confirmed[$id] ?? 0),
                'waiting' => DbValue::int($waiting[$id] ?? 0),
                'max' => DbValue::int($maxParticipants[$id] ?? 0),
            ];
        }

        return $counts;
    }
}
