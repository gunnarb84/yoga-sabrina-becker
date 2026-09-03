<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Activity;

enum ActivityStatus: string
{
    case Draft = 'entwurf';

    case Published = 'veroeffentlicht';

    case Completed = 'abgeschlossen';

    case Cancelled = 'storniert';
}
