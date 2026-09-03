<?php

declare(strict_types=1);

namespace Yoga\Platform\NumberSequence\Application;

interface NextNumber
{
    /**
     * Reserviert und liefert die nächste fortlaufende Nummer
     * für die Belegart mit dem gegebenen Code.
     *
     * @throws \RuntimeException wenn kein Nummernkreis für den Code konfiguriert ist
     */
    public function next(string $code): string;
}
