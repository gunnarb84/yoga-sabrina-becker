<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\Invoices;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class InvoicesQuery
{
    /**
     * @return list<object{id: string, nummer: string, ausgestellt_am: string, empfaenger: string, betrag: string, waehrung: string, status: string, zahlung_id: string}>
     */
    public function execute(): array
    {
        $rows = DB::table('verwaltung_rechnungen')
            ->select([
                'verwaltung_rechnungen.id',
                'verwaltung_rechnungen.nummer',
                'verwaltung_rechnungen.ausgestellt_am',
                'verwaltung_rechnungen.empfaenger',
                'verwaltung_rechnungen.betrag',
                'verwaltung_rechnungen.waehrung',
                'verwaltung_rechnungen.status',
                'verwaltung_rechnungen.zahlung_id',
            ])
            ->orderByDesc('verwaltung_rechnungen.ausgestellt_am')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id) || ! is_string($row->zahlung_id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'nummer' => DbValue::string($row->nummer),
                'ausgestellt_am' => DbValue::string($row->ausgestellt_am),
                'empfaenger' => DbValue::string($row->empfaenger),
                'betrag' => DbValue::string($row->betrag),
                'waehrung' => DbValue::string($row->waehrung),
                'status' => DbValue::string($row->status),
                'zahlung_id' => Uuid::fromBytes($row->zahlung_id)->toString(),
            ];
        }

        return $result;
    }
}
