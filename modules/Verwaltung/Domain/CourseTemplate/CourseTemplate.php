<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\CourseTemplate;

use Illuminate\Database\Eloquent\Model;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

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
