<?php

declare(strict_types=1);

namespace Yoga\Platform\NumberSequence\Domain;

use Illuminate\Database\Eloquent\Model;
use Yoga\Platform\Identity\UuidCast;
use Yoga\Platform\Shared\Domain\PlatformEntityLifecycle;

/**
 * Zentrale, fachlich neutrale Definition eines fortlaufenden Nummernkreises.
 * Keine Modulbegriffe, keine Tabellennamen von Anwendungsmodulen.
 */
final class NumberSequenceDefinition extends Model
{
    use PlatformEntityLifecycle;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'nummernkreis_definitionen';

    protected $guarded = [];

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'jahr_stellen' => 'int',
        'start_nummer' => 'int',
        'laenge_nummer' => 'int',
        'version' => 'int',
    ];

    public function fullPrefix(): string
    {
        return $this->prefix . '-' . $this->currentYearFormatted();
    }

    public function formatNumber(int $number): string
    {
        return sprintf(
            '%s-%s',
            $this->fullPrefix(),
            str_pad((string) $number, $this->laenge_nummer, '0', STR_PAD_LEFT),
        );
    }

    public function currentYear(): int
    {
        return (int) now()->format('Y');
    }

    private function currentYearFormatted(): string
    {
        $year = (string) $this->currentYear();

        return substr($year, -$this->jahr_stellen);
    }
}
