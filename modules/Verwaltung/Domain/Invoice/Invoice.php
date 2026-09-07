<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Invoice;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
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
 * @property InvoiceStatus $status
 * @property int $version
 * @property string $waehrung
 * @property string $zahlung_id
 */
class Invoice extends BaseModel
{
    protected $table = 'verwaltung_rechnungen';

    protected $guarded = [];

    public static function findById(string $id): ?self
    {
        return self::query()->whereRaw('id = ?', [Uuid::fromString($id)->getBytes()])->first();
    }

    protected $casts = [
        'id' => UuidCast::class,
        'zahlung_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'ausgestellt_am' => 'datetime',
        'status' => InvoiceStatus::class,
        'betrag' => 'decimal:4',
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<Payment, $this>
     */
    public function zahlung(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'zahlung_id');
    }
}
