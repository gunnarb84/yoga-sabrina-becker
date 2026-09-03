<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CourseTemplate\CourseTemplates;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class CourseTemplatesQuery
{
    /**
     * @return list<object{
     *     id: string,
     *     titel: string,
     *     wochentag: string,
     *     startzeit: string,
     *     dauer_minuten: int,
     *     anzahl_termine: int,
     *     preis: string,
     *     maximale_teilnehmerzahl: int,
     *     ort: string|null
     * }>
     */
    public function execute(int $limit = 100): array
    {
        $rows = DB::table('verwaltung_kursvorlagen')
            ->select([
                'id',
                'titel',
                'wochentag',
                'startzeit',
                'dauer_minuten',
                'anzahl_termine',
                'preis',
                'maximale_teilnehmerzahl',
                'ort',
            ])
            ->orderBy('titel')
            ->limit($limit)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'titel' => DbValue::string($row->titel),
                'wochentag' => DbValue::string($row->wochentag),
                'startzeit' => DbValue::string($row->startzeit),
                'dauer_minuten' => DbValue::int($row->dauer_minuten),
                'anzahl_termine' => DbValue::int($row->anzahl_termine),
                'preis' => DbValue::string($row->preis),
                'maximale_teilnehmerzahl' => DbValue::int($row->maximale_teilnehmerzahl),
                'ort' => DbValue::nullableString($row->ort),
            ];
        }

        return $result;
    }
}
