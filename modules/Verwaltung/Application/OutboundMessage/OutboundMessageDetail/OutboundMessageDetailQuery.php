<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\OutboundMessageDetail;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final readonly class OutboundMessageDetailQuery
{
    /**
     * @return object{id: string, anmeldung_id: string|null, aktivitaet_id: string|null, empfaenger: string, betreff: string, inhalt: string, status: string, versendet_am: string|null, fehlermeldung: string|null}|null
     */
    public function execute(string $messageId): ?object
    {
        $messageIdBytes = Uuid::fromString($messageId)->getBytes();

        $row = DB::table('verwaltung_ausgehende_nachrichten')
            ->select([
                'verwaltung_ausgehende_nachrichten.id',
                'verwaltung_ausgehende_nachrichten.anmeldung_id',
                'verwaltung_anmeldungen.aktivitaet_id',
                'verwaltung_ausgehende_nachrichten.empfaenger',
                'verwaltung_ausgehende_nachrichten.betreff',
                'verwaltung_ausgehende_nachrichten.inhalt',
                'verwaltung_ausgehende_nachrichten.status',
                'verwaltung_ausgehende_nachrichten.versendet_am',
                'verwaltung_ausgehende_nachrichten.fehlermeldung',
            ])
            ->whereRaw('verwaltung_ausgehende_nachrichten.id = ?', [$messageIdBytes])
            ->leftJoin('verwaltung_anmeldungen', 'verwaltung_ausgehende_nachrichten.anmeldung_id', '=', 'verwaltung_anmeldungen.id')
            ->first();

        if ($row === null) {
            return null;
        }

        return (object) [
            'id' => Uuid::fromBytes($row->id)->toString(),
            'anmeldung_id' => $row->anmeldung_id === null ? null : Uuid::fromBytes($row->anmeldung_id)->toString(),
            'aktivitaet_id' => $row->aktivitaet_id === null ? null : Uuid::fromBytes($row->aktivitaet_id)->toString(),
            'empfaenger' => $row->empfaenger,
            'betreff' => $row->betreff,
            'inhalt' => $row->inhalt,
            'status' => $row->status,
            'versendet_am' => $row->versendet_am,
            'fehlermeldung' => $row->fehlermeldung,
        ];
    }
}
