<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Registration;

enum RegistrationSource: string
{
    case Website = 'webseite';

    case Administration = 'verwaltung';
}
