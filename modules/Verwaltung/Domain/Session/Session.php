<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Session;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

class Session extends Model
{
    use EntityLifecycle;

    protected $table = 'verwaltung_termine';

    protected $guarded = [];

    protected $casts = [
        'id' => UuidCast::class,
        'aktivitaet_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'beginn' => 'datetime',
        'ende' => 'datetime',
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<Activity, $this>
     */
    public function aktivitaet(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'aktivitaet_id');
    }
}
