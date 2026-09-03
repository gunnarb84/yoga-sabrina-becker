<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\CashReturn;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

class CashReturn extends Model
{
    use EntityLifecycle;

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
