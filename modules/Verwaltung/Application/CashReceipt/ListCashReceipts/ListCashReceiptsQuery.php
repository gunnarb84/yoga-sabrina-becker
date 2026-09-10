<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashReceipt\ListCashReceipts;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class ListCashReceiptsQuery
{
    /**
     * Standardseitengröße der Liste.
     */
    private const PAGE_SIZE = 50;

    /**
     * @return list<object{id: string, nummer: string, ausgestellt_am: string, empfaenger: string, betrag: string, waehrung: string}>
     */
    public function execute(?string $nummerFilter = null, ?string $empfaengerFilter = null): array
    {
        $query = DB::table('verwaltung_bareinnahmenbelege')
            ->select([
                'id',
                'nummer',
                'ausgestellt_am',
                'empfaenger',
                'betrag',
                'waehrung',
            ]);

        if ($nummerFilter !== null && $nummerFilter !== '') {
            $query->where('nummer', 'like', '%'.$nummerFilter.'%');
        }

        if ($empfaengerFilter !== null && $empfaengerFilter !== '') {
            $query->where('empfaenger', 'like', '%'.$empfaengerFilter.'%');
        }

        $rows = $query
            ->orderByDesc('ausgestellt_am')
            ->orderByDesc('nummer')
            ->limit(self::PAGE_SIZE)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'nummer' => DbValue::string($row->nummer),
                'ausgestellt_am' => DbValue::string($row->ausgestellt_am),
                'empfaenger' => DbValue::string($row->empfaenger),
                'betrag' => DbValue::string($row->betrag),
                'waehrung' => DbValue::string($row->waehrung),
            ];
        }

        return $result;
    }
}
