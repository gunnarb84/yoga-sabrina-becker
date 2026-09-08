<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\ContactInquiry\ListContactInquiries;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class ListContactInquiries
{
    /**
     * Standardseitengröße der Liste.
     */
    private const PAGE_SIZE = 50;

    /**
     * @return list<object{id: string, empfangen_am: string, name: string, email: string, anlass: string|null, status: string}>
     */
    public function execute(?string $statusFilter = null): array
    {
        $query = DB::table('webseite_kontaktanfragen')
            ->select([
                'id',
                'empfangen_am',
                'name',
                'email',
                'anlass',
                'status',
            ]);

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $rows = $query
            ->orderByDesc('empfangen_am')
            ->orderByDesc('id')
            ->limit(self::PAGE_SIZE)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->id)) {
                continue;
            }

            $result[] = (object) [
                'id' => Uuid::fromBytes($row->id)->toString(),
                'empfangen_am' => DbValue::string($row->empfangen_am),
                'name' => DbValue::string($row->name),
                'email' => DbValue::string($row->email),
                'anlass' => DbValue::nullableString($row->anlass),
                'status' => DbValue::string($row->status),
            ];
        }

        return $result;
    }
}
