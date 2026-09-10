<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordCashPaymentBatch;

/**
 * Eine zur Erfassung vorgesehene Bar-Anmeldung der Massenerfassung.
 */
final readonly class Item
{
    public function __construct(
        public string $registrationId,
        public ?string $paidAt = null,
    ) {
    }
}
