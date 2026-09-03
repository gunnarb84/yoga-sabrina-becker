<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\Participants;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class ParticipantsQuery
{
    /**
     * @return list<object{id: string, email: string, vorname: string, nachname: string, telefon: string|null, stadt: string|null}>
     */
    public function execute(?string $search = null, int $limit = 100): array
    {
        $query = DB::table('verwaltung_teilnehmer')
            ->select([
                'id',
                'email',
                'vorname',
                'nachname',
                'telefon',
                'stadt',
            ]);

        if ($search !== null && $search !== '') {
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('vorname', 'like', $term)
                    ->orWhere('nachname', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        $rows = $query->orderBy('nachname')
            ->orderBy('vorname')
            ->limit($limit)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'email' => DbValue::string($row->email),
                'vorname' => DbValue::string($row->vorname),
                'nachname' => DbValue::string($row->nachname),
                'telefon' => DbValue::nullableString($row->telefon),
                'stadt' => DbValue::nullableString($row->stadt),
            ];
        }

        return $result;
    }
}
