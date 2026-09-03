<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\OutboundMessage;

enum OutboundMessageStatus: string
{
    case Pending = 'ausstehend';

    case Sent = 'versandt';

    case Failed = 'fehlgeschlagen';
}
