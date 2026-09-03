<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Payment;

enum PaymentMethod: string
{
    case Cash = 'bar';

    case Transfer = 'ueberweisung';
}
