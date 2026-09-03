<?php

declare(strict_types=1);

namespace Yoga\Platform\Shared\Application;

use InvalidArgumentException;

final readonly class DbValue
{
    public static function string(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value) || $value instanceof \Stringable) {
            return (string) $value;
        }

        throw new InvalidArgumentException('Expected a string value from the database.');
    }

    public static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::string($value);
    }

    public static function int(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) || is_float($value)) {
            return (int) $value;
        }

        throw new InvalidArgumentException('Expected an integer value from the database.');
    }

    public static function nullableInt(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return self::int($value);
    }

    public static function bool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value !== 0;
        }

        if (is_string($value)) {
            return $value !== '0' && $value !== '';
        }

        throw new InvalidArgumentException('Expected a boolean value from the database.');
    }
}
