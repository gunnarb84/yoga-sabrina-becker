<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\CourseTemplate;

use Illuminate\Database\Eloquent\Model;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property int $anzahl_termine
 * @property int $dauer_minuten
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property string|null $kurzbeschreibung
 * @property string|null $langbeschreibung
 * @property int $maximale_teilnehmerzahl
 * @property string|null $ort
 * @property string $preis
 * @property string $titel
 * @property int $version
 * @property string $waehrung
 * @property string $wochentag
 */
class CourseTemplate extends Model
{
    use EntityLifecycle;

    protected $table = 'verwaltung_kursvorlagen';

    protected $guarded = [];

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'preis' => 'decimal:4',
        'maximale_teilnehmerzahl' => 'int',
        'dauer_minuten' => 'int',
        'anzahl_termine' => 'int',
        'version' => 'int',
    ];
}
