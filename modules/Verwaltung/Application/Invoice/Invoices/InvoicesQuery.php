<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Invoice\Invoices;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

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

        return $rows->map(function (object $row): object {
            return (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'nummer' => $row->nummer,
                'ausgestellt_am' => $row->ausgestellt_am,
                'empfaenger' => $row->empfaenger,
                'betrag' => $row->betrag,
                'waehrung' => $row->waehrung,
                'status' => $row->status,
                'zahlung_id' => Uuid::fromBytes($row->zahlung_id)->toString(),
            ];
        })->toArray();
    }
}
