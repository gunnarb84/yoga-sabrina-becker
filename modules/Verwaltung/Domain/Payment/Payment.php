<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

class Payment extends Model
{
    use EntityLifecycle;

    protected $table = 'verwaltung_zahlungen';

    protected $guarded = [];

    public static function findById(string $id): ?self
    {
        return self::query()->whereRaw('id = ?', [Uuid::fromString($id)->getBytes()])->first();
    }

    protected $casts = [
        'id' => UuidCast::class,
        'anmeldung_id' => UuidCast::class,
        'beleg_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'methode' => PaymentMethod::class,
        'betrag' => 'decimal:4',
        'bezahlt_am' => 'datetime',
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<Registration, $this>
     */
    public function anmeldung(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'anmeldung_id');
    }
}
