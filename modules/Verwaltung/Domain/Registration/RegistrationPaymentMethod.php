<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Registration;

enum RegistrationPaymentMethod: string
{
    case Cash = 'bar';

    case Transfer = 'ueberweisung';

    case Free = 'kostenlos';
}
