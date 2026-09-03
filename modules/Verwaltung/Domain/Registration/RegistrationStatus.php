<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Registration;

enum RegistrationStatus: string
{
    case Confirmed = 'bestaetigt';

    case WaitingList = 'warteliste';

    case Cancelled = 'storniert';
}
