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

        usort($movements, fn (object $a, object $b): int => [$a->datum, $a->angelegt_am] <=> [$b->datum, $b->angelegt_am]);

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
     * @return list<object{id: string, typ: string, richtung: string, datum: string, kennung: string, beschreibung: string, betrag: string, waehrung: string, angelegt_am: string, bestand: string}>
     */
    private function receipts(?string $nummerFilter, ?string $empfaengerFilter): array
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
    private function returns(?string $nummerFilter, ?string $empfaengerFilter): array
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
    private function withdrawals(?string $nummerFilter, ?string $empfaengerFilter): array
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
