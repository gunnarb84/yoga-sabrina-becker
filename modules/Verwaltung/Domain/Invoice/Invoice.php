<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

/**
 * @property InvoiceStatus $status
 */
class Invoice extends Model
{
    use EntityLifecycle;

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
