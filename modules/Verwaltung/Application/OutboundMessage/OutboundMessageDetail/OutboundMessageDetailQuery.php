<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\OutboundMessageDetail;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

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

        if (! is_string($row->id)) {
            return null;
        }

        return (object) [
            'id' => Uuid::fromBytes($row->id)->toString(),
            'anmeldung_id' => is_string($row->anmeldung_id) ? Uuid::fromBytes($row->anmeldung_id)->toString() : null,
            'aktivitaet_id' => is_string($row->aktivitaet_id) ? Uuid::fromBytes($row->aktivitaet_id)->toString() : null,
            'empfaenger' => DbValue::string($row->empfaenger),
            'betreff' => DbValue::string($row->betreff),
            'inhalt' => DbValue::string($row->inhalt),
            'status' => DbValue::string($row->status),
            'versendet_am' => DbValue::nullableString($row->versendet_am),
            'fehlermeldung' => DbValue::nullableString($row->fehlermeldung),
        ];
    }
}
