<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Session\SessionsByActivity;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final readonly class SessionsByActivityQuery
{
    /**
     * @return list<object{id: string, beginn: string, ende: string, ort: string|null, hinweis: string|null, vergangen: bool}>
     */
    public function execute(string $activityId): array
    {
        $activityIdBytes = Uuid::fromString($activityId)->getBytes();
        $now = now();

        $rows = DB::table('verwaltung_termine')
            ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->orderBy('beginn')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id) || ! is_string($row->beginn) || ! is_string($row->ende)) {
                continue;
            }

            $beginn = Carbon::parse($row->beginn);
            $ende = Carbon::parse($row->ende);

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'beginn' => $beginn->format('Y-m-d\TH:i'),
                'ende' => $ende->format('Y-m-d\TH:i'),
                'ort' => is_string($row->ort) ? $row->ort : null,
                'hinweis' => is_string($row->hinweis) ? $row->hinweis : null,
                'vergangen' => $ende->isBefore($now),
            ];
        }

        return $result;
    }
}
