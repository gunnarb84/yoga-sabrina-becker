<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\Participants;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final readonly class ParticipantsQuery
{
    /**
     * @return list<object{id: string, email: string, vorname: string, nachname: string, telefon: string|null, stadt: string|null}>
     */
    public function execute(int $limit = 100): array
    {
        $rows = DB::table('verwaltung_teilnehmer')
            ->select([
                'id',
                'email',
                'vorname',
                'nachname',
                'telefon',
                'stadt',
            ])
            ->orderBy('nachname')
            ->orderBy('vorname')
            ->limit($limit)
            ->get();

        return $rows->map(function (object $row): object {
            return (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'email' => $row->email,
                'vorname' => $row->vorname,
                'nachname' => $row->nachname,
                'telefon' => $row->telefon,
                'stadt' => $row->stadt,
            ];
        })->toArray();
    }
}
