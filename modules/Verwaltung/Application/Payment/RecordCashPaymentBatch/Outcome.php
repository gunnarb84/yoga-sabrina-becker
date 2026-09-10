<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch;

/**
 * Ergebnis einer einzelnen Anmeldung innerhalb der Massenerfassung:
 * `erfasst` (Zahlung und Beleg entstanden) oder `uebersprungen` (Fachprüfung
 * griff, der Vorgang nennt den Fehlercode).
 */
final readonly class Outcome
{
    public function __construct(
        public string $registrationId,
        public string $outcome,
        public ?string $errorCode = null,
        public ?string $receiptNumber = null,
    ) {
    }
}
