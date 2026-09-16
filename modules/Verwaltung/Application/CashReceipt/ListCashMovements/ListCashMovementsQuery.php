<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashReceipt\ListCashMovements;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

/**
 * Liefert alle Kassenbewegungen — Bareinnahmenbelege, Bar-Rückzahlungen und
 * Barentnahmen — gemischt-chronologisch mit dem laufenden Bestand je Zeile.
 * Der Bestand wird aufsteigend kumuliert (Bareinnahmen als Zugang,
 * Rückzahlungen und Barentnahmen als Abgang), angezeigt wird neueste zuerst.
 */
final readonly class ListCashMovementsQuery
{
    /**
     * Standardseitengröße der Liste.
     */
    private const PAGE_SIZE = 50;

    /**
     * Obergrenze je Bewegungsart, über die der Bestand berechnet wird.
     */
    private const MAX_PER_TYPE = 5000;

    public const TYPE_RECEIPT = 'bareinnahme';

    public const TYPE_RETURN = 'rueckgabe';

    public const TYPE_WITHDRAWAL = 'barentnahme';

    public const DIRECTION_IN = 'einnahme';

    public const DIRECTION_OUT = 'ausgabe';

    /**
     * @return list<object{id: string, typ: string, richtung: string, datum: string, kennung: string, beschreibung: string, betrag: string, waehrung: string, bestand: string}>
     */
    public function execute(?string $nummerFilter = null, ?string $empfaengerFilter = null): array
    {
        $movements = [
            ...$this->receipts($nummerFilter, $empfaengerFilter),
            ...$this->returns($nummerFilter, $empfaengerFilter),
            ...$this->withdrawals($nummerFilter, $empfaengerFilter),
        ];

        usort($movements, self::sortForCashBook(...));

        $bestand = 0.0;
        $withBalance = [];

        foreach ($movements as $movement) {
            $bestand += $movement->richtung === self::DIRECTION_IN
                ? (float) $movement->betrag
                : -(float) $movement->betrag;

            $withBalance[] = (object) [
                'id' => $movement->id,
                'typ' => $movement->typ,
                'richtung' => $movement->richtung,
                'datum' => $movement->datum,
                'kennung' => $movement->kennung,
                'beschreibung' => $movement->beschreibung,
                'betrag' => $movement->betrag,
                'waehrung' => $movement->waehrung,
                'bestand' => (string) $bestand,
            ];
        }

        // Neueste zuerst: die aufsteigend kumulierten Bewegungen werden umgedreht.
        return array_slice(array_reverse($withBalance), 0, self::PAGE_SIZE);
    }

    /**
     * Kassenbuch-Auszug für einen Monat (Format `YYYY-MM`): alle Bewegungen des
     * Zeitraums aufsteigend, der laufende Bestand beginnt mit dem Übertrag aus
     * den Vormonaten (unabhängig von den Textfiltern).
     *
     * @return object{movements: list<object{id: string, typ: string, richtung: string, datum: string, kennung: string, beschreibung: string, betrag: string, waehrung: string, bestand: string}>, uebertrag: string, endbestand: string}|null
     */
    public function executeForMonth(string $monat, ?string $nummerFilter, ?string $empfaengerFilter): ?object
    {
        try {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $monat);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            return null;
        }

        // Fehlende Tagangabe: der Start des Monats ist der einzige Sinn.
        if (! $start instanceof \Carbon\Carbon || $start->format('Y-m') !== $monat) {
            return null;
        }

        $beginn = $start->startOfMonth();
        $ende = $start->copy()->startOfMonth()->addMonth();

        $movements = [
            ...$this->receipts($nummerFilter, $empfaengerFilter, $beginn, $ende),
            ...$this->returns($nummerFilter, $empfaengerFilter, $beginn, $ende),
            ...$this->withdrawals($nummerFilter, $empfaengerFilter, $beginn, $ende),
        ];

        usort($movements, self::sortForCashBook(...));

        $uebertrag = $this->openingBalance($beginn);
        $bestand = $uebertrag;

        $withBalance = [];

        foreach ($movements as $movement) {
            $bestand += $movement->richtung === self::DIRECTION_IN
                ? (float) $movement->betrag
                : -(float) $movement->betrag;

            $withBalance[] = (object) [
                'id' => $movement->id,
                'typ' => $movement->typ,
                'richtung' => $movement->richtung,
                'datum' => $movement->datum,
                'kennung' => $movement->kennung,
                'beschreibung' => $movement->beschreibung,
                'betrag' => $movement->betrag,
                'waehrung' => $movement->waehrung,
                'bestand' => (string) $bestand,
            ];
        }

        return (object) [
            'movements' => $withBalance,
            'uebertrag' => (string) $uebertrag,
            'endbestand' => (string) $bestand,
        ];
    }

    /**
     * Summe aller Bewegungen vor dem Beginn des Zeitraums — der Übertrag aus
     * den Vormonaten, unabhängig von den Textfiltern.
     */
    private function openingBalance(\Carbon\CarbonInterface $beginn): float
    {
        $einnahmen = (float) DB::table('verwaltung_bareinnahmenbelege')
            ->where('ausgestellt_am', '<', $beginn)
            ->sum('betrag');
        $rueckzahlungen = (float) DB::table('verwaltung_rueckgabebestaetigungen')
            ->where('ausgestellt_am', '<', $beginn)
            ->sum('betrag');
        $barentnahmen = (float) DB::table('verwaltung_barentnahmen')
            ->where('datum', '<', $beginn->toDateString())
            ->sum('betrag');

        return $einnahmen - $rueckzahlungen - $barentnahmen;
    }

    /**
     * Sortierfolge für das Kassenbuch: aufsteigend nach Datum; innerhalb eines
     * Tages nummerierte Bewegungen nach Belegnummer, dahinter Barentnahmen ohne
     * Belegnummer, untereinander nach Erfassungszeit.
     *
     * @param object{datum: string, typ: string, kennung: string, angelegt_am: string} $a
     * @param object{datum: string, typ: string, kennung: string, angelegt_am: string} $b
     */
    private static function sortForCashBook(object $a, object $b): int
    {
        // Belege führen Datum mit Uhrzeit, Barentnahmen nur das Datum — der
        // Tagesvergleich nutzt den Datumsteil.
        $byDate = substr($a->datum, 0, 10) <=> substr($b->datum, 0, 10);

        if ($byDate !== 0) {
            return $byDate;
        }

        $aNumbered = $a->typ !== self::TYPE_WITHDRAWAL;
        $bNumbered = $b->typ !== self::TYPE_WITHDRAWAL;

        if ($aNumbered !== $bNumbered) {
            return $aNumbered ? -1 : 1;
        }

        if ($aNumbered) {
            $byNumber = strcmp($a->kennung, $b->kennung);

            if ($byNumber !== 0) {
                return $byNumber;
            }
        }

        return $a->angelegt_am <=> $b->angelegt_am;
    }

    /**
     * @return list<object{id: string, typ: string, richtung: string, datum: string, kennung: string, beschreibung: string, betrag: string, waehrung: string, angelegt_am: string, bestand: string}>
     */
    private function receipts(?string $nummerFilter, ?string $empfaengerFilter, ?\Carbon\CarbonInterface $beginn = null, ?\Carbon\CarbonInterface $ende = null): array
    {
        $query = DB::table('verwaltung_bareinnahmenbelege')
            ->select([
                'id',
                'nummer AS kennung',
                'ausgestellt_am AS datum',
                'empfaenger AS beschreibung',
                'betrag',
                'waehrung',
                'angelegt_am',
            ])
            ->limit(self::MAX_PER_TYPE);

        if ($beginn !== null && $ende !== null) {
            $query->where('ausgestellt_am', '>=', $beginn)->where('ausgestellt_am', '<', $ende);
        }

        if ($nummerFilter !== null && $nummerFilter !== '') {
            $query->where('nummer', 'like', '%'.$nummerFilter.'%');
        }

        if ($empfaengerFilter !== null && $empfaengerFilter !== '') {
            $query->where('empfaenger', 'like', '%'.$empfaengerFilter.'%');
        }

        $result = [];

        foreach ($query->get() as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'typ' => self::TYPE_RECEIPT,
                'richtung' => self::DIRECTION_IN,
                'datum' => DbValue::string($row->datum),
                'kennung' => DbValue::string($row->kennung),
                'beschreibung' => DbValue::string($row->beschreibung),
                'betrag' => DbValue::string($row->betrag),
                'waehrung' => DbValue::string($row->waehrung),
                'angelegt_am' => DbValue::string($row->angelegt_am),
                'bestand' => '0',
            ];
        }

        return $result;
    }

    /**
     * @return list<object{id: string, typ: string, richtung: string, datum: string, kennung: string, beschreibung: string, betrag: string, waehrung: string, angelegt_am: string, bestand: string}>
     */
    private function returns(?string $nummerFilter, ?string $empfaengerFilter, ?\Carbon\CarbonInterface $beginn = null, ?\Carbon\CarbonInterface $ende = null): array
    {
        $query = DB::table('verwaltung_rueckgabebestaetigungen')
            ->select([
                'id',
                'nummer AS kennung',
                'ausgestellt_am AS datum',
                'empfaenger AS beschreibung',
                'betrag',
                'waehrung',
                'angelegt_am',
            ])
            ->limit(self::MAX_PER_TYPE);

        if ($beginn !== null && $ende !== null) {
            $query->where('ausgestellt_am', '>=', $beginn)->where('ausgestellt_am', '<', $ende);
        }

        if ($nummerFilter !== null && $nummerFilter !== '') {
            $query->where('nummer', 'like', '%'.$nummerFilter.'%');
        }

        if ($empfaengerFilter !== null && $empfaengerFilter !== '') {
            $query->where('empfaenger', 'like', '%'.$empfaengerFilter.'%');
        }

        $result = [];

        foreach ($query->get() as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'typ' => self::TYPE_RETURN,
                'richtung' => self::DIRECTION_OUT,
                'datum' => DbValue::string($row->datum),
                'kennung' => DbValue::string($row->kennung),
                'beschreibung' => DbValue::string($row->beschreibung),
                'betrag' => DbValue::string($row->betrag),
                'waehrung' => DbValue::string($row->waehrung),
                'angelegt_am' => DbValue::string($row->angelegt_am),
                'bestand' => '0',
            ];
        }

        return $result;
    }

    /**
     * @return list<object{id: string, typ: string, richtung: string, datum: string, kennung: string, beschreibung: string, betrag: string, waehrung: string, angelegt_am: string, bestand: string}>
     */
    private function withdrawals(?string $nummerFilter, ?string $empfaengerFilter, ?\Carbon\CarbonInterface $beginn = null, ?\Carbon\CarbonInterface $ende = null): array
    {
        $query = DB::table('verwaltung_barentnahmen')
            ->select([
                'id',
                'fremdbelegnummer AS kennung',
                'datum',
                'zweck AS beschreibung',
                'betrag',
                'angelegt_am',
            ])
            ->limit(self::MAX_PER_TYPE);

        if ($beginn !== null && $ende !== null) {
            $query->where('datum', '>=', $beginn)->where('datum', '<', $ende);
        }

        if ($nummerFilter !== null && $nummerFilter !== '') {
            $query->where('fremdbelegnummer', 'like', '%'.$nummerFilter.'%');
        }

        if ($empfaengerFilter !== null && $empfaengerFilter !== '') {
            $query->where('zweck', 'like', '%'.$empfaengerFilter.'%');
        }

        $result = [];

        foreach ($query->get() as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'typ' => self::TYPE_WITHDRAWAL,
                'richtung' => self::DIRECTION_OUT,
                'datum' => DbValue::string($row->datum),
                'kennung' => DbValue::nullableString($row->kennung) ?? '',
                'beschreibung' => DbValue::string($row->beschreibung),
                'betrag' => DbValue::string($row->betrag),
                'waehrung' => 'EUR',
                'angelegt_am' => DbValue::string($row->angelegt_am),
                'bestand' => '0',
            ];
        }

        return $result;
    }
}
