<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\NumberSequence;

use Yoga\Platform\NumberSequence\Application\NumberSequenceResolver;

final class VerwaltungNumberSequenceResolver implements NumberSequenceResolver
{
    private const TABLE = 'verwaltung_nummernkreise';

    public function resolve(string $code): string
    {
        return self::TABLE;
    }
}
