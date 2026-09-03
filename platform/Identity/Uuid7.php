<?php

declare(strict_types=1);

namespace Yoga\Platform\Identity;

use Ramsey\Uuid\Uuid;

final class Uuid7
{
    /**
     * Erzeugt eine UUID Version 7 (zeitlich sortiert) als String.
     * Wird fuer alle Geschaeftsentitaeten als Primaerschluessel verwendet.
     */
    public static function generateString(): string
    {
        return Uuid::uuid7()->toString();
    }
}
