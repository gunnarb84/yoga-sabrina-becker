<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\OutboundMessage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property string|null $anmeldung_id
 * @property string $betreff
 * @property string $empfaenger
 * @property string|null $fehlermeldung
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property string $inhalt
 * @property OutboundMessageStatus $status
 * @property \Carbon\Carbon|null $versendet_am
 * @property int $version
 */
class OutboundMessage extends Model
{
    use EntityLifecycle;

    protected $table = 'verwaltung_ausgehende_nachrichten';

    protected $guarded = [];

    public static function findById(string $id): ?self
    {
        return self::query()->whereRaw('id = ?', [Uuid::fromString($id)->getBytes()])->first();
    }

    protected $casts = [
        'id' => UuidCast::class,
        'anmeldung_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'versendet_am' => 'datetime',
        'status' => OutboundMessageStatus::class,
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<Registration, $this>
     */
    public function anmeldung(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'anmeldung_id');
    }

    public function markSent(): void
    {
        $this->status = OutboundMessageStatus::Sent;
        $this->versendet_am = now();
    }

    public function markFailed(string $reason): void
    {
        $this->status = OutboundMessageStatus::Failed;
        $this->fehlermeldung = $reason;
    }
}
