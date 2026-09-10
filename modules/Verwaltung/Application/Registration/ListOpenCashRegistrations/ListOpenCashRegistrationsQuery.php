<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\ListOpenCashRegistrations;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class ListOpenCashRegistrationsQuery
{
    /**
     * Standardseitengröße der Liste.
     */
    private const PAGE_SIZE = 50;

    /**
     * Liefert die offenen Bar-Anmeldungen einer Veranstaltung zur Massenerfassung:
     * Zahlungsart Bar, Status Bestätigt, noch keine Zahlung erfasst.
     *
     * @return list<object{registrationId: string, empfaenger: string, betrag: string, waehrung: string}>
     */
    public function execute(string $activityId): array
    {
        $activityIdBytes = Uuid::fromString($activityId)->getBytes();

        $rows = DB::table('verwaltung_anmeldungen')
            ->join('verwaltung_teilnehmer', 'verwaltung_teilnehmer.id', '=', 'verwaltung_anmeldungen.teilnehmer_id')
            ->join('verwaltung_aktivitaeten', 'verwaltung_aktivitaeten.id', '=', 'verwaltung_anmeldungen.aktivitaet_id')
            ->leftJoin('verwaltung_zahlungen', 'verwaltung_zahlungen.anmeldung_id', '=', 'verwaltung_anmeldungen.id')
            ->where('verwaltung_anmeldungen.aktivitaet_id', '=', $activityIdBytes)
            ->where('verwaltung_anmeldungen.zahlungsart', '=', RegistrationPaymentMethod::Cash->value)
            ->where('verwaltung_anmeldungen.status', '=', RegistrationStatus::Confirmed->value)
            ->where('verwaltung_anmeldungen.zahlungsstatus', '!=', RegistrationPaymentStatus::Paid->value)
            ->whereNull('verwaltung_zahlungen.id')
            ->select([
                'verwaltung_anmeldungen.id as anmeldung_id',
                'verwaltung_teilnehmer.vorname',
                'verwaltung_teilnehmer.nachname',
                'verwaltung_aktivitaeten.preis as betrag',
                'verwaltung_aktivitaeten.waehrung',
            ])
            ->orderBy('verwaltung_teilnehmer.nachname')
            ->orderBy('verwaltung_teilnehmer.vorname')
            ->limit(self::PAGE_SIZE)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if (! is_string($row->anmeldung_id)) {
                continue;
            }

            $result[] = (object) [
                'registrationId' => Uuid::fromBytes($row->anmeldung_id)->toString(),
                'empfaenger' => trim(DbValue::string($row->vorname).' '.DbValue::string($row->nachname)),
                'betrag' => DbValue::string($row->betrag),
                'waehrung' => DbValue::string($row->waehrung),
            ];
        }

        return $result;
    }
}
