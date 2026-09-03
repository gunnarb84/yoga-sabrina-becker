<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Activity;

enum ActivityType: string
{
    case Course = 'kurs';

    case Event = 'event';

    case Workshop = 'workshop';
}
