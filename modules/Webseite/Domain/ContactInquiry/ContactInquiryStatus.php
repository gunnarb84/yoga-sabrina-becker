<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Domain\ContactInquiry;

enum ContactInquiryStatus: string
{
    case New = 'neu';

    case InProgress = 'in_bearbeitung';

    case Done = 'erledigt';
}
