<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptListPdf;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Throwable;
use Yoga\Modules\Verwaltung\Application\CashReceipt\ListCashMovements\ListCashMovementsQuery;
use Yoga\Platform\Shared\Application\Result;

/**
 * Monatsdruck der Bareinnahmenliste: alle Kassenbewegungen eines Monats
 * aufsteigend, mit Übertrag aus den Vormonaten als Eröffnungszeile und dem
 * Endbestand als Abschlusszeile.
 */
final readonly class GenerateCashReceiptListPdf
{
    public function __construct(
        private ListCashMovementsQuery $movements,
    ) {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $period = $this->movements->executeForMonth($request->monat, null, null);

        if ($period === null) {
            return Result::failure('cash_receipt_list.period_invalid');
        }

        $rows = [];

        foreach ($period->movements as $movement) {
            $rows[] = (object) [
                'datum' => Carbon::parse($movement->datum)->format('d.m.Y'),
                'art' => match ($movement->typ) {
                    'bareinnahme' => 'Bareinnahme',
                    'rueckgabe' => 'Rückgabe',
                    'barentnahme' => 'Barentnahme',
                    default => $movement->typ,
                },
                'kennung' => $movement->kennung,
                'beschreibung' => $movement->beschreibung,
                'einnahme' => $movement->richtung === 'einnahme' ? $movement->betrag : null,
                'ausgabe' => $movement->richtung === 'ausgabe' ? $movement->betrag : null,
                'bestand' => $movement->bestand,
                'waehrung' => $movement->waehrung,
            ];
        }

        try {
            $monat = Carbon::parse($request->monat.'-01');
            $monat->locale('de');
            $monatLabel = $monat->translatedFormat('F Y');

            $html = view('bareinnahmenbelege.liste-pdf', [
                'monatLabel' => $monatLabel,
                'uebertrag' => $period->uebertrag,
                'endbestand' => $period->endbestand,
                'rows' => $rows,
            ])->render();

            $options = new Options([
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'defaultFont' => 'Lato',
            ]);

            $dompdf = new Dompdf($options);
            $this->registerLato($dompdf);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4');
            $dompdf->render();

            return Result::success(new Response(
                $dompdf->output(),
                'Bareinnahmenliste-'.$request->monat.'.pdf',
            ));
        } catch (Throwable $e) {
            return Result::failure('cash_receipt_list.pdf_generation_failed', [$e->getMessage()]);
        }
    }

    private function registerLato(Dompdf $dompdf): void
    {
        $fontDirectory = resource_path('fonts');
        $fontMetrics = $dompdf->getFontMetrics();

        foreach (['Lato-Regular.ttf' => ['normal', 'normal'], 'Lato-Bold.ttf' => ['normal', 'bold']] as $file => [$style, $weight]) {
            $path = $fontDirectory.'/'.$file;

            if (is_file($path)) {
                $fontMetrics->registerFont(
                    ['family' => 'Lato', 'style' => $style, 'weight' => $weight],
                    $path,
                );
            }
        }
    }
}
