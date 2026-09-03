<?php

declare(strict_types=1);

namespace Yoga\Platform\NumberSequence\Domain;

use Illuminate\Database\Eloquent\Model;
use Yoga\Platform\Identity\UuidCast;
use Yoga\Platform\Shared\Domain\PlatformEntityLifecycle;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property string $bezeichnung
 * @property string $code
 * @property \Carbon\Carbon $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property int $jahr_stellen
 * @property int $laenge_nummer
 * @property string $prefix
 * @property int $start_nummer
 * @property int $version
 */
class NumberSequenceDefinition extends Model
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
