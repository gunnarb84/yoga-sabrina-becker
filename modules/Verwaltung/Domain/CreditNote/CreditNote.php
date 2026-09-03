<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\CreditNote;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

class CreditNote extends Model
{
    use EntityLifecycle;

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
