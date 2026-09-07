<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Support;

use Illuminate\Database\Eloquent\Model;
use Yoga\Platform\Identity\Uuid7;

/**
 * Lifecycle-Logik fuer Entitaeten des Verwaltungs-Moduls.
 * Ergaenzt die Plattformvorgaben um modulspezifische Defaults.
 */
trait EntityLifecycle
{
    protected static function bootEntityLifecycle(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Uuid7::generateString());
            }

            if ($model->getAttribute('version') === null) {
                $model->setAttribute('version', 1);
            }
        });

        static::updating(function (Model $model): void {
            $version = $model->getAttribute('version');
            if (! is_int($version)) {
                throw new \RuntimeException('Version must be an integer');
            }

            $model->setAttribute('version', $version + 1);
        });
    }
}
