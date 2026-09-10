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

    /**
     * Setzt den Zählerstand der Belegart mindestens auf die gegebene Nummer.
     * Wird für die Übernahme/Nachpflege bereits vergebener Belegnummern
     * genutzt, damit der Nummernkreis kollisionsfrei hinter der höchsten
     * übernommenen Nummer weiterzählt. Ohne Jahr gilt das laufende Jahr.
     *
     * @throws \RuntimeException wenn kein Nummernkreis für den Code konfiguriert ist
     */
    public function advance(string $code, int $letzteNummer, ?int $jahr = null): void;
}
