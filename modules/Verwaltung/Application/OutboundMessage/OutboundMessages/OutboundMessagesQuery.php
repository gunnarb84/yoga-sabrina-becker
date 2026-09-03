<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\OutboundMessages;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class OutboundMessagesQuery
{
    /**
     * @return list<object{id: string, anmeldung_id: string|null, empfaenger: string, betreff: string, status: string, versendet_am: string|null}>
     */
    public function execute(?string $statusFilter = null, ?string $recipientFilter = null): array
    {
        $query = DB::table('verwaltung_ausgehende_nachrichten')
            ->select([
                'verwaltung_ausgehende_nachrichten.id',
                'verwaltung_ausgehende_nachrichten.anmeldung_id',
                'verwaltung_ausgehende_nachrichten.empfaenger',
                'verwaltung_ausgehende_nachrichten.betreff',
                'verwaltung_ausgehende_nachrichten.status',
                'verwaltung_ausgehende_nachrichten.versendet_am',
            ]);

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('verwaltung_ausgehende_nachrichten.status', $statusFilter);
        }

        if ($recipientFilter !== null && $recipientFilter !== '') {
            $query->where('verwaltung_ausgehende_nachrichten.empfaenger', 'like', '%'.$recipientFilter.'%');
        }

        $rows = $query
            ->orderByDesc('verwaltung_ausgehende_nachrichten.angelegt_am')
            ->orderByDesc('verwaltung_ausgehende_nachrichten.id')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'anmeldung_id' => is_string($row->anmeldung_id) ? Uuid::fromBytes($row->anmeldung_id)->toString() : null,
                'empfaenger' => DbValue::string($row->empfaenger),
                'betreff' => DbValue::string($row->betreff),
                'status' => DbValue::string($row->status),
                'versendet_am' => DbValue::nullableString($row->versendet_am),
            ];
        }

        return $result;
    }
}
