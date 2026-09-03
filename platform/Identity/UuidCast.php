<?php

declare(strict_types=1);

namespace Yoga\Platform\Identity;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Wandelt UUIDs zwischen binaerer Speicherung (binary(16)) und textueller
 * Darstellung im Modell um.
 */
final class UuidCast implements CastsAttributes
{
    /**
     * @param  string|null  $value
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return Uuid::fromBytes($value)->toString();
    }

    /**
     * @param  string|null  $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return Uuid::fromString($value)->getBytes();
    }
}
