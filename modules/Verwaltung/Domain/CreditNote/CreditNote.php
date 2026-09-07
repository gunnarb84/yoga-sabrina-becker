<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\CreditNote;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Support\BaseModel;
use Yoga\Platform\Identity\UuidCast;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property \Carbon\Carbon $ausgestellt_am
 * @property string $betrag
 * @property string $empfaenger
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property string $nummer
 * @property string $rechnung_id
 * @property int $version
 * @property string $waehrung
 */
class CreditNote extends BaseModel
{
    protected $table = 'verwaltung_gutschriften';

    protected $guarded = [];

    protected $casts = [
        'id' => UuidCast::class,
        'rechnung_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'ausgestellt_am' => 'datetime',
        'betrag' => 'decimal:4',
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function rechnung(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'rechnung_id');
    }
}
