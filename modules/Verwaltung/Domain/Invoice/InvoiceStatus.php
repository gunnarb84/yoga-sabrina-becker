<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Invoice;

enum InvoiceStatus: string
{
    case Open = 'offen';

    case Paid = 'bezahlt';
}
