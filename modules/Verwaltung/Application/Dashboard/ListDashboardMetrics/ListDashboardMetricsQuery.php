<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Dashboard\ListDashboardMetrics;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yoga\Platform\Shared\Application\DbValue;

/**
 * Liefert die Kennzahlen und Zähler des Dashboards: Anmeldungen und Bareinnahmen
 * im laufenden Monat, Kassenbestand über alle Zeiten sowie die Zähler der offenen
 * Aufgaben (offene Rechnungen, fehlgeschlagene Nachrichten, Wartelisteneinträge).
 */
final readonly class ListDashboardMetricsQuery
{
    /**
     * @return object{registrationsThisMonth: int, cashReceiptsMonth: string, cashReceiptsMonthCurrency: string, cashBalance: string, openInvoices: int, failedMessages: int, waitingListEntries: int}
     */
    public function execute(): object
    {
        $monatsstart = Carbon::now()->startOfMonth();
        $monatsende = Carbon::now()->startOfMonth()->addMonth();

        $monatsbelege = DB::table('verwaltung_bareinnahmenbelege')
            ->where('ausgestellt_am', '>=', $monatsstart)
            ->where('ausgestellt_am', '<', $monatsende);

        $einnahmenMonat = (float) (clone $monatsbelege)->sum('betrag');
        $waehrung = $einnahmenMonat > 0 ? DbValue::string($monatsbelege->value('waehrung')) : '';

        $einnahmen = (float) DB::table('verwaltung_bareinnahmenbelege')->sum('betrag');
        $rueckzahlungen = (float) DB::table('verwaltung_rueckgabebestaetigungen')->sum('betrag');
        $barentnahmen = (float) DB::table('verwaltung_barentnahmen')->sum('betrag');

        return (object) [
            'registrationsThisMonth' => (int) DB::table('verwaltung_anmeldungen')
                ->where('angelegt_am', '>=', $monatsstart)
                ->where('angelegt_am', '<', $monatsende)
                ->where('status', '!=', 'storniert')
                ->count(),
            'cashReceiptsMonth' => (string) $einnahmenMonat,
            'cashReceiptsMonthCurrency' => $waehrung !== '' ? $waehrung : 'EUR',
            'cashBalance' => (string) ($einnahmen - $rueckzahlungen - $barentnahmen),
            'openInvoices' => (int) DB::table('verwaltung_rechnungen')
                ->where('status', 'offen')
                ->count(),
            'failedMessages' => (int) DB::table('verwaltung_ausgehende_nachrichten')
                ->where('status', 'fehlgeschlagen')
                ->count(),
            'waitingListEntries' => (int) DB::table('verwaltung_anmeldungen')
                ->where('status', 'warteliste')
                ->count(),
        ];
    }
}
