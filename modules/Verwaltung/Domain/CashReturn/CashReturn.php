<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\CashReturn;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Support\BaseModel;
use Yoga\Platform\Identity\UuidCast;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property \Carbon\Carbon $ausgestellt_am
 * @property string $bareinnahmenbeleg_id
 * @property string $betrag
 * @property string $empfaenger
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property string $nummer
 * @property int $version
 * @property string $waehrung
 */
class CashReturn extends BaseModel
{
    protected $table = 'verwaltung_rueckgabebestaetigungen';

    protected $guarded = [];

    protected $casts = [
        'id' => UuidCast::class,
        'bareinnahmenbeleg_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'ausgestellt_am' => 'datetime',
        'betrag' => 'decimal:4',
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<CashReceipt, $this>
     */
    public function bareinnahmenbeleg(): BelongsTo
    {
        return $this->belongsTo(CashReceipt::class, 'bareinnahmenbeleg_id');
    }
}
