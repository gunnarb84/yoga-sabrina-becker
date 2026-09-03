<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\WaitingList;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

class WaitingList extends Model
{
    use EntityLifecycle;

    protected $table = 'verwaltung_warteliste';

    protected $guarded = [];

    protected $casts = [
        'id' => UuidCast::class,
        'anmeldung_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'rang' => 'int',
        'nachgerueckt_am' => 'datetime',
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<Registration, $this>
     */
    public function anmeldung(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'anmeldung_id');
    }

    public function promote(): void
    {
        $this->nachgerueckt_am = now();
    }
}
