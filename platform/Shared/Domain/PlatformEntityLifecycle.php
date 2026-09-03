<?php

declare(strict_types=1);

namespace Yoga\Platform\Shared\Domain;

use Illuminate\Database\Eloquent\Model;
use Yoga\Platform\Identity\Uuid7;

/**
 * Lifecycle-Logik fuer Plattform-Entitaeten: UUID-v7-Primaerschluessel,
 * Zeitstempel und Zeilenversion. Fachliche Entitaeten der Module erhalten
 * eine eigene Basisklasse im Modul.
 */
trait PlatformEntityLifecycle
{
    /**
     * Wird von Eloquent automatisch aufgerufen, weil der Trait-Name
     * PlatformEntityLifecycle lautet und diese Methode bootPlatformEntityLifecycle
     * heisst. Initialisiert Primaerschluessel, Anlagezeitpunkt und Version beim
     * ersten Speichern und aktualisiert Aenderungszeitpunkt und Version danach.
     */
    protected static function bootPlatformEntityLifecycle(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Uuid7::generateString());
            }

            if ($model->getAttribute('angelegt_am') === null) {
                $model->setAttribute('angelegt_am', now());
            }

            if ($model->getAttribute('version') === null) {
                $model->setAttribute('version', 1);
            }
        });

        static::updating(function (Model $model): void {
            $model->setAttribute('geaendert_am', now());
            $model->setAttribute('version', (int) $model->getAttribute('version') + 1);
        });
    }
}
