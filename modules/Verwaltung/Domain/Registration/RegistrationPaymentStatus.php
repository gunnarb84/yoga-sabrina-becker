<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Registration;

enum RegistrationPaymentStatus: string
{
    case Open = 'offen';

    case Paid = 'bezahlt';

    case Refunded = 'erstattet';
}
