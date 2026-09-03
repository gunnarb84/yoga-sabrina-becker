<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\OutboundMessages;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

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

        return $rows->map(function (object $row): object {
            return (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'anmeldung_id' => $row->anmeldung_id === null ? null : Uuid::fromBytes($row->anmeldung_id)->toString(),
                'empfaenger' => $row->empfaenger,
                'betreff' => $row->betreff,
                'status' => $row->status,
                'versendet_am' => $row->versendet_am,
            ];
        })->toArray();
    }
}
