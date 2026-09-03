<?php

declare(strict_types=1);

namespace Yoga\Platform\NumberSequence\Application;

/**
 * Wird vom fachlichen Modul implementiert. Teilt dem Nummernkreis-Baustein mit,
 * in welcher Tabelle und Spalte die Nummern der jeweiligen Belegart gespeichert werden.
 */
interface NumberSequenceResolver
{
    /**
     * Liefert den Tabellennamen, in dem das Modul den Zählerstand
     * für die Belegart mit dem gegebenen Code speichert. Die Tabelle muss
     * die Spalten `code`, `jahr` und `letzte_nummer` enthalten.
     */
    public function resolve(string $code): string;
}
